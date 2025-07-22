<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\KitchenView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('restaurant.waiter', compact('restaurant', 'categories', 'products', 'activeOrders', 'readyOrders'));
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
            'status' => 'pending',
            'total' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
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

        $todayOrdersCount = $restaurant->getTodayOrdersCount();

        return view('restaurant.kitchen', compact('restaurant', 'pendingOrders', 'preparingOrders', 'readyOrders', 'todayOrdersCount'));
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

        $paymentPendingOrders = $restaurant->orders()
            ->where('status', 'ready')
            ->with(['orderItems.product'])
            ->orderBy('created_at')
            ->get();

        $recentPayments = $restaurant->orders()
            ->where('status', 'delivered')
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
            'recentPayments', 
            'todayRevenue', 
            'todayOrdersCount', 
            'completedOrdersCount'
        ));
    }

    // Kasa - Ödeme İşle
    public function processPayment(Request $request, $orderId)
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

        $request->validate([
            'payment_method' => 'required|in:nakit,kart',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        // Ödeme bilgilerini order'a ekle (eğer order tablosunda payment alanları varsa)
        $order->update([
            'status' => 'delivered',
            'payment_method' => $request->payment_method,
            'cash_received' => $request->cash_received,
            'paid_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödeme başarıyla tamamlandı!',
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

        return view('restaurant.menu-management', compact('restaurant', 'categories', 'products'));
    }

    // Kategori İşlemleri
    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
}
