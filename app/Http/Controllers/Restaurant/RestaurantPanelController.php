<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\KitchenView;
use App\Models\Table;
use App\Models\RestaurantOrderSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RestaurantPanelController extends Controller
{
    // Restoran Dashboard (Tüm Roller)
    public function dashboard()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        $stats = [
            'today_orders' => $restaurant->getTodayOrdersCount(),
            'yesterday_orders' => $restaurant->orders()->whereDate('created_at', today()->subDay())->count(),
            'today_revenue' => $restaurant->getTodayRevenue(),
            'active_orders' => $restaurant->orders()
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count(),
            'active_staff' => $restaurant->staff()->where('is_active', true)->count(),
        ];

        $recentOrders = $restaurant->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->take(10)
            ->get();

        $activeOrders = $restaurant->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return view('restaurant.dashboard', compact('restaurant', 'stats', 'recentOrders', 'activeOrders'));
    }

    // Garson Paneli
    public function waiter()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        $categories = $restaurant->categories()->with('products')->get();
        // Tüm mevcut ürünleri (stokta ve menüde aktif) al
        $products = $restaurant->products()->where('is_available', true)->orderBy('sort_order')->get();
        $tables = $restaurant->tables()->where('is_active', true)->orderBy('table_number')->get();
        $activeOrders = $restaurant->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        $readyOrders = $restaurant->orders()
            ->where('status', 'ready')
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        // Garsonun kendi oluşturduğu siparişler
        $myOrders = $restaurant->orders()
            ->where('created_by_user_id', $user->id)
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered'])
            ->with(['orderItems.product', 'createdBy'])
            ->latest()
            ->get();



        return view('restaurant.waiter', compact('restaurant', 'categories', 'products', 'tables', 'activeOrders', 'readyOrders', 'myOrders'));
    }

    // Garson - Sipariş Oluştur
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        $request->validate([
            'table_number' => 'required|string|max:50',
            'customer_name' => 'nullable|string|max:100',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string',
        ]);

        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'table_number' => $request->table_number,
            'customer_name' => $request->customer_name,
            'created_by_user_id' => $user->id,
            'status' => 'pending',
            'total' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            // Stok kontrolü ve düşme
            if (!$product->decreaseStock($item['quantity'])) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' ürününde yetersiz stok!'
                ], 400);
            }
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'note' => $item['note'] ?? null,
            ]);

            $total += $product->price * $item['quantity'];
        }

        $order->update(['total' => $total]);

        // Mutfağa bildirim
        KitchenView::create([
            'order_id' => $order->id,
            'seen' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla oluşturuldu!',
            'order_id' => $order->id,
        ]);
    }

    // Garson - Siparişi Teslim Edildi Olarak İşaretle
    public function markAsDelivered(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişe erişim yetkiniz yok.'], 403);
        }

        $order->update(['status' => 'delivered']);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş teslim edildi olarak işaretlendi!',
        ]);
    }

    // Garson - Siparişi İptal Et
    public function cancelOrder(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->where('created_by_user_id', $user->id)
                     ->whereIn('status', ['pending'])
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişi iptal edemezsiniz.'], 403);
        }

        // Stokları geri ver
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            if ($product->stock !== -1) {  // Sınırsız stok değilse
                $product->increment('stock', $item->quantity);
            }
        }

        $order->update([
            'status' => 'cancelled',
            'last_status' => $order->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş iptal edildi ve stoklar geri yüklendi!',
        ]);
    }

    // Garson - Sipariş Masa Numarası Güncelle
    public function updateOrderTable(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $request->validate([
            'table_number' => 'required|string|max:50',
        ]);

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->where('created_by_user_id', $user->id)
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişi düzenleyemezsiniz.'], 403);
        }

        $order->update(['table_number' => $request->table_number]);

        return response()->json([
            'success' => true,
            'message' => 'Masa numarası güncellendi!',
        ]);
    }

    // Mutfak Paneli
    public function kitchen()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }
        $pendingOrders = $restaurant->orders()
            ->where('status', 'pending')
            ->with(['orderItems.product'])
            ->orderBy('created_at')
            ->get();
        $preparingOrders = $restaurant->orders()
            ->where('status', 'preparing')
            ->with(['orderItems.product'])
            ->orderBy('created_at')
            ->get();
        $readyOrders = $restaurant->orders()
            ->where('status', 'ready')
            ->with(['orderItems.product'])
            ->orderBy('created_at')
            ->get();
        $cancelledOrders = $restaurant->orders()
            ->whereIn('status', ['cancelled', 'musteri_iptal', 'zafiyat'])
            ->where(function($q){
                $q->where('cancelled_by_customer', true)->orWhere('status', 'musteri_iptal')->orWhere('status', 'zafiyat');
            })
            ->with(['orderItems.product'])
            ->orderByDesc('updated_at')
            ->get();
        $todayOrdersCount = $restaurant->getTodayOrdersCount();
        return view('restaurant.kitchen', compact('restaurant', 'pendingOrders', 'preparingOrders', 'readyOrders', 'cancelledOrders', 'todayOrdersCount'));
    }

    // Mutfak - Zafiyat işlemi
    public function markAsZafiyat(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }
        $order = $restaurant->orders()->where('id', $orderId)
            ->whereIn('status', ['cancelled', 'musteri_iptal', 'zafiyat'])
            ->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Sipariş bulunamadı veya uygun değil.'], 404);
        }
        $order->update(['status' => 'zafiyat']);
        // Burada kasiyer panelinde zafiyat olarak gösterilecek
        return response()->json(['success' => true, 'message' => 'Sipariş zafiyat olarak işaretlendi.']);
    }

    // Mutfak - Sipariş Hazırlamaya Başla
    public function startPreparing(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişe erişim yetkiniz yok.'], 403);
        }

        $order->update(['status' => 'preparing']);

        // Kitchen view'ı seen olarak işaretle
        KitchenView::where('order_id', $order->id)->update([
            'seen' => true,
            'seen_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş hazırlanmaya başlandı!',
        ]);
    }

    // Mutfak - Siparişi Hazır Olarak İşaretle
    public function markAsReady(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişe erişim yetkiniz yok.'], 403);
        }

        $order->update(['status' => 'ready']);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş hazır olarak işaretlendi! Garson bilgilendirildi.',
        ]);
    }

    // Kasa Paneli
    public function cashier()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        // Masa bazlı ödeme bekleyen siparişleri grupla
        $paymentPendingOrders = $restaurant->orders()
            ->where('status', 'delivered')
            ->with(['orderItems.product'])
            ->get()
            ->groupBy('table_number');

        // Masa bazlı açık (ödenmemiş) siparişleri grupla
        $openOrders = $restaurant->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered'])
            ->with(['orderItems.product', 'createdBy'])
            ->orderBy('created_at')
            ->get()
            ->groupBy('table_number');

        // Bugünkü tamamlanan ödemeler
        $recentPayments = $restaurant->orders()
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->with(['orderItems.product'])
            ->orderByDesc('created_at')
            ->get();

        $todayRevenue = $restaurant->getTodayRevenue();
        $todayOrdersCount = $restaurant->getTodayOrdersCount();
        $completedOrdersCount = $recentPayments->count();

        return view('restaurant.cashier', compact(
            'restaurant', 
            'paymentPendingOrders',
            'openOrders',
            'recentPayments', 
            'todayRevenue', 
            'todayOrdersCount', 
            'completedOrdersCount'
        ));
    }

    // Kasa - Masa Bazlı Ödeme İşle
    public function processTablePayment(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $request->validate([
            'table_number' => 'required|string',
            'payments' => 'required|array',
            'payments.*.method' => 'required|in:nakit,kart',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        // Masanın açık siparişlerini al
        $tableOrders = $restaurant->orders()
            ->where('table_number', $request->table_number)
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered'])
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada ödeme bekleyen sipariş bulunamadı.'], 404);
        }

        // Toplam tutarı hesapla
        $totalAmount = $tableOrders->sum('total');
        $paymentTotal = collect($request->payments)->sum('amount');

        if (abs($totalAmount - $paymentTotal) > 0.01) { // Küsurat farkını tolere et
            return response()->json([
                'success' => false, 
                'message' => 'Ödeme tutarı toplam fatura tutarı ile eşleşmiyor.'
            ], 400);
        }

        // Ödeme detaylarını hazırla
        $paymentDetails = [
            'methods' => $request->payments,
            'total_paid' => $paymentTotal,
            'payment_date' => now()
        ];

        // Tüm siparişleri completed yap ve ödeme bilgilerini ekle
        foreach ($tableOrders as $order) {
            $order->update([
                'status' => 'completed',
                'payment_method' => json_encode($paymentDetails),
                'cash_received' => $paymentTotal, // Toplam ödenen tutar
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Masa {$request->table_number} için ödeme başarıyla tamamlandı!",
            'completed_orders' => $tableOrders->count(),
            'total_amount' => $totalAmount
        ]);
    }

    // Masa Detaylarını Getir (AJAX)
    public function getTableDetails(Request $request, $tableNumber)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $tableOrders = $restaurant->orders()
            ->where('table_number', $tableNumber)
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered'])
            ->with(['orderItems.product', 'createdBy'])
            ->orderBy('created_at')
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada açık sipariş bulunamadı.'], 404);
        }

        $totalAmount = $tableOrders->sum('total');

        return response()->json([
            'success' => true,
            'table_number' => $tableNumber,
            'orders' => $tableOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                    'customer_name' => $order->customer_name,
                    'created_at' => $order->created_at->toISOString(),
                    'updated_at' => $order->updated_at->toISOString(),
                    'created_by' => $order->createdBy ? [
                        'id' => $order->createdBy->id,
                        'name' => $order->createdBy->name
                    ] : null,
                    'order_items' => $order->orderItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'price' => (float) $item->price,
                            'note' => $item->note,
                            'product' => [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'price' => (float) $item->product->price
                            ]
                        ];
                    })
                ];
            }),
            'total_amount' => (float) $totalAmount,
            'order_count' => $tableOrders->count()
        ]);
    }

    // Kasa - Fiş Yazdır
    public function printReceipt(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $order = Order::where('id', $orderId)
                     ->where('restaurant_id', $restaurant->id)
                     ->with(['orderItems.product', 'restaurant'])
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişe erişim yetkiniz yok.'], 403);
        }

        return view('restaurant.receipt', compact('order', 'restaurant'));
    }

    // Menü Yönetimi
    public function menuManagement()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        $categories = $restaurant->categories()->withCount('products')->orderBy('sort_order')->get();
        $products = $restaurant->products()->with('category')->orderBy('sort_order')->get();
        $tables = $restaurant->tables()->orderBy('table_number')->get();

        return view('restaurant.menu-management', compact('restaurant', 'categories', 'products', 'tables'));
    }

    // Kategori İşlemleri
    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }
        
        // İşletmenin paket limitlerini kontrol et
        $business = $restaurant->business;
        if (!$business->hasActiveSubscription()) {
            return response()->json(['success' => false, 'message' => 'Kategori eklemek için aktif bir paket aboneliğiniz olması gerekiyor.'], 403);
        }
        
        // Kategori sayısı limitini kontrol et - Restoran bazında
        $maxCategories = $business->getFeatureLimit('max_categories');
        $currentCategoryCount = $restaurant->categories()->count();
        
        if ($maxCategories !== null && $maxCategories !== 0 && $currentCategoryCount >= $maxCategories) {
            return response()->json([
                'success' => false, 
                'message' => 'Paket limitine ulaştınız lütfen işletmeniz ile görüşünüz'
            ], 403);
        }
        
        // Aynı isimli kategori kontrolü
        $categoryExists = $restaurant->categories()->where('name', $request->name)->exists();
        if ($categoryExists) {
            return response()->json([
                'success' => false, 
                'message' => 'Bu isimde bir kategori zaten mevcut!'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Aynı isimli kategori kontrolü (kendi hariç)
        $categoryExists = $restaurant->categories()
            ->where('name', $request->name)
            ->where('id', '!=', $category->id)
            ->exists();
        if ($categoryExists) {
            return response()->json([
                'success' => false, 
                'message' => 'Bu isimde bir kategori zaten mevcut!'
            ], 400);
        }

        $data = [
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
        ];

        // Resim yükleme
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_category_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $category = $restaurant->categories()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla eklendi!',
            'category' => $category
        ]);
    }

    public function updateCategory(Request $request, $categoryId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $category = $restaurant->categories()->findOrFail($categoryId);

        $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? $category->sort_order,
        ];

        // Resim yükleme
        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_category_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla güncellendi!',
            'category' => $category
        ]);
    }

    public function deleteCategory($categoryId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $category = $restaurant->categories()->findOrFail($categoryId);

        // Kategoriye bağlı ürünler varsa kategoriyi null yap
        $category->products()->update(['category_id' => null]);
        
        // Kategori resmini sil
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla silindi!'
        ]);
    }

    // Kategori detaylarını getir (edit için)
    public function getCategory($categoryId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $category = $restaurant->categories()->findOrFail($categoryId);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    // Ürün İşlemleri
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }
        
        // İşletmenin paket limitlerini kontrol et
        $business = $restaurant->business;
        if (!$business->hasActiveSubscription()) {
            return response()->json(['success' => false, 'message' => 'Ürün eklemek için aktif bir paket aboneliğiniz olması gerekiyor.'], 403);
        }
        
        // Ürün sayısı limitini kontrol et - Restoran bazında
        $maxProducts = $business->getFeatureLimit('max_products');
        $currentProductCount = $restaurant->products()->count();
        
        if ($maxProducts !== null && $maxProducts !== 0 && $currentProductCount >= $maxProducts) {
            return response()->json([
                'success' => false, 
                'message' => 'Paket limitine ulaştınız lütfen işletmeniz ile görüşünüz'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock ?? 100,
            'is_available' => $request->is_available ?? true,
            'sort_order' => $request->sort_order ?? 0,
        ];

        // Resim yükleme
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_product_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product = $restaurant->products()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla eklendi!',
            'product' => $product->load('category')
        ]);
    }

    public function updateProduct(Request $request, $productId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $product = $restaurant->products()->findOrFail($productId);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock ?? $product->stock,
            'is_available' => $request->is_available ?? $product->is_available,
            'sort_order' => $request->sort_order ?? $product->sort_order,
        ];

        // Resim yükleme
        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_product_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla güncellendi!',
            'product' => $product->load('category')
        ]);
    }

    public function deleteProduct($productId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $product = $restaurant->products()->findOrFail($productId);
        
        // Ürün resmini sil
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla silindi!'
        ]);
    }

    // Ürün detaylarını getir (edit için)
    public function getProduct($productId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $product = $restaurant->products()->with('category')->findOrFail($productId);

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    // Yardımcı method - Kullanıcının bağlı olduğu restoranı getir
    private function getUserRestaurant($user)
    {
        // Admin ise tüm restoranlara erişebilir (test için)
        if ($user->isAdmin()) {
            return Restaurant::first();
        }

        // İşletme sahibi ise ilk restoranına erişir
        if ($user->isBusinessOwner()) {
            $businesses = $user->getActiveBusinesses();
            if ($businesses->isNotEmpty()) {
                return $businesses->first()->restaurants()->first();
            }
        }

        // Restoran yöneticisi ise
        if ($user->isRestaurantManager()) {
            return $user->managedRestaurants()->first();
        }

        // Diğer roller için staff tablosundan kontrol et
        $staffRecord = $user->restaurantStaff()
            ->where('is_active', true)
            ->with('restaurant')
            ->first();

        return $staffRecord ? $staffRecord->restaurant : null;
    }

    // API - Canlı sipariş durumu
    public function getOrderUpdates()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $pendingOrders = $restaurant->orders()->where('status', 'pending')->count();
        $preparingOrders = $restaurant->orders()->where('status', 'preparing')->count();
        $readyOrders = $restaurant->orders()->where('status', 'ready')->count();

        return response()->json([
            'pending_orders' => $pendingOrders,
            'preparing_orders' => $preparingOrders,
            'ready_orders' => $readyOrders,
            'total_notifications' => $pendingOrders + $readyOrders,
        ]);
    }

    // Masa Yönetimi Metodları
    public function storeTables(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $request->validate([
            'table_number' => 'required|string|max:20',
            'capacity' => 'nullable|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        // Aynı masa numarasının olup olmadığını kontrol et
        $existingTable = $restaurant->tables()->where('table_number', $request->table_number)->first();
        if ($existingTable) {
            return response()->json(['success' => false, 'message' => 'Bu masa numarası zaten mevcut!'], 400);
        }

        $table = $restaurant->tables()->create([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Masa başarıyla eklendi!',
            'table' => $table
        ]);
    }

    public function updateTable(Request $request, $tableId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $table = $restaurant->tables()->findOrFail($tableId);

        $request->validate([
            'table_number' => 'required|string|max:20',
            'capacity' => 'nullable|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Aynı masa numarasının başka masada olup olmadığını kontrol et
        $existingTable = $restaurant->tables()
            ->where('table_number', $request->table_number)
            ->where('id', '!=', $tableId)
            ->first();
        
        if ($existingTable) {
            return response()->json(['success' => false, 'message' => 'Bu masa numarası zaten mevcut!'], 400);
        }

        $table->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Masa başarıyla güncellendi!',
            'table' => $table
        ]);
    }

    public function deleteTable($tableId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $table = $restaurant->tables()->findOrFail($tableId);

        // Aktif siparişi olan masayı silmeyi engelle
        $activeOrders = $restaurant->orders()
            ->where('table_number', $table->table_number)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();

        if ($activeOrders > 0) {
            return response()->json(['success' => false, 'message' => 'Bu masanın aktif siparişi var, silinemez!'], 400);
        }

        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Masa başarıyla silindi!'
        ]);
    }

    public function getTable($tableId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $table = $restaurant->tables()->findOrFail($tableId);

        return response()->json([
            'success' => true,
            'table' => $table
        ]);
    }

    // Sipariş Ayarları
    public function storeOrderSettings(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $request->validate([
            'ordering_enabled' => 'boolean',
            'enabled_categories' => 'required|string',
        ]);

        $enabledCategories = json_decode($request->enabled_categories, true);

        $restaurant->orderSettings()->updateOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'ordering_enabled' => $request->boolean('ordering_enabled'),
                'enabled_categories' => $enabledCategories,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Sipariş ayarları başarıyla güncellendi!'
        ]);
    }

    // Kasiyer - Gün Sonu PDF Raporu
    public function endOfDayPdf(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        if (!$restaurant) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }
        $today = now()->toDateString();
        $orders = $restaurant->orders()
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->with(['orderItems.product'])
            ->orderBy('created_at')
            ->get();
        $totalRevenue = $orders->sum('total');
        $totalCount = $orders->count();
        $payments = [];
        foreach ($orders as $order) {
            $paymentData = json_decode($order->payment_method, true);
            if (isset($paymentData['methods'])) {
                foreach ($paymentData['methods'] as $method) {
                    $payments[$method['method']] = ($payments[$method['method']] ?? 0) + $method['amount'];
                }
            }
        }
        $pdf = Pdf::loadView('restaurant.cashier_endofday_pdf', [
            'restaurant' => $restaurant,
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalCount' => $totalCount,
            'payments' => $payments,
            'today' => $today,
        ]);
        return $pdf->stream('gunsonu_'.$today.'.pdf');
    }

    // Mutfak - Klasik iptal işlemi
    public function kitchenCancelOrder(Request $request, $orderId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }
        $order = $restaurant->orders()->where('id', $orderId)
            ->whereIn('status', ['cancelled', 'musteri_iptal', 'zafiyat'])
            ->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Sipariş bulunamadı veya uygun değil.'], 404);
        }
        $order->update(['status' => 'kitchen_cancelled']);
        return response()->json(['success' => true, 'message' => 'Sipariş mutfak tarafından iptal edildi.']);
    }
}
