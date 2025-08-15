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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RestaurantPanelController extends Controller
{
    /**
     * QR kod üretir ve kaydeder
     */
    protected function generateQRCode($url)
    {
        return QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->generate($url);
    }

    // Restoran Dashboard (Tüm Roller)
    public function dashboard()
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            // Kullanıcının hiç restoranı yoksa veya erişim yetkisi yoksa
            if ($user->isBusinessOwner()) {
                return redirect()->route('business.dashboard')->with('error', 'Henüz restoranınız bulunmuyor. Lütfen önce bir restoran oluşturun.');
            } elseif ($user->isRestaurantManager()) {
                return redirect()->route('business.dashboard')->with('error', 'Henüz size atanmış bir restoran bulunmuyor.');
            } else {
                abort(403, 'Bu restorana erişim yetkiniz yok.');
            }
        }

        $stats = [
            'today_orders' => $restaurant ? $restaurant->getTodayOrdersCount() : 0,
            'yesterday_orders' => $restaurant ? $restaurant->orders()->whereDate('created_at', today()->subDay())->count() : 0,
            'today_revenue' => $restaurant ? $restaurant->getTodayRevenue() : 0,
            'active_orders' => $restaurant ? $restaurant->orders()
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count() : 0,
            'active_staff' => $restaurant ? $restaurant->staff()->where('is_active', true)->count() : 0,
        ];

        $recentOrders = $restaurant ? $restaurant->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->take(10)
            ->get() : collect();

        $activeOrders = $restaurant ? $restaurant->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with(['orderItems.product'])
            ->latest()
            ->get() : collect();

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

    // Kategori İşlemleri
    public function storeCategory(Request $request)
    {
        try {
            $user = Auth::user();
            $restaurant = $this->getUserRestaurant($user);

            if (!$restaurant) {
                return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
            }
            
            // İşletmenin paket limitlerini kontrol et
            $business = $restaurant->business;
            if (!$business->hasActiveSubscription()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kategori eklemek için aktif bir paket aboneliğiniz olması gerekiyor.'
                ], 403);
            }
            
            $request->validate([
                'name' => 'required|string|max:100',
                'sort_order' => 'nullable|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Aynı isimli kategori kontrolü (sadece bu restoran için)
            $categoryExists = Category::where('restaurant_id', $restaurant->id)
                                   ->where('name', $request->name)
                                   ->exists();
            if ($categoryExists) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bu restoranda aynı isimde bir kategori zaten mevcut!'
                ], 400);
            }

            // Kategori sayısı limitini kontrol et - Restoran bazında
            $maxCategories = $business->getFeatureLimit('max_categories');
            $currentCategoryCount = $restaurant->categories()->count();
            
            if ($maxCategories !== null && $maxCategories !== 0 && $currentCategoryCount >= $maxCategories) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Maksimum kategori sayısına ulaştınız. Daha fazla kategori eklemek için paketinizi yükseltmelisiniz.'
                ], 403);
            }

            $data = [
                'restaurant_id' => $restaurant->id,
                'name' => $request->name,
                'sort_order' => $request->sort_order ?? 0,
            ];

            // Resim yükleme
            if ($request->hasFile('image')) {
                try {
                    $image = $request->file('image');
                    $imageName = time() . '_category_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('categories', $imageName, 'public');
                    $data['image'] = $imagePath;
                } catch (\Exception $e) {
                    \Log::error('Kategori resmi yükleme hatası: ' . $e->getMessage());
                    // Resim yükleme başarısız olsa bile kategori eklenebilir
                }
            }

            $category = $restaurant->categories()->create($data);

            if (!$category) {
                throw new \Exception('Kategori veritabanına eklenemedi');
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla eklendi!',
                'category' => $category->load('products') // İlişkili ürünleri de getir
            ]);

        } catch (\Exception $e) {
            \Log::error('Kategori ekleme hatası: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Kategori eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
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

        $order->updateStatus('delivered', 'Garson tarafından teslim edildi');

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

        $order->updateStatus('cancelled', 'Garson tarafından iptal edildi');

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
        $order->updateStatus('zafiyat', 'Kasiyer tarafından zafiyat olarak işaretlendi');
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

        $order->updateStatus('preparing', 'Mutfak tarafından hazırlanmaya başlandı');

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

        $order->updateStatus('ready', 'Mutfak tarafından hazır olarak işaretlendi');

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

        // Masa bazlı açık (ödenmemiş) siparişleri grupla
        $openOrders = $restaurant->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered', 'zafiyat', 'kitchen_cancelled'])
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereNull('payment_status');
            })
            ->whereNull('session_id') // Sadece açık masadaki siparişler
            ->with(['orderItems.product', 'createdBy', 'payments'])
            ->orderBy('created_at')
            ->get()
            ->groupBy('table_number');

        // Bugünkü ödemeler (tamamlanmış ve kısmi ödemeler) - Session bazlı gruplandırılmış
        $recentPayments = $restaurant->orders()
            ->where(function($query) {
                $query->where('status', 'delivered')
                      ->orWhere('payment_status', 'paid')
                      ->orWhere('payment_status', 'partially_paid');
            })
            ->whereDate('created_at', today())
            ->whereDate('updated_at', today()) // Sadece bugün güncellenen siparişler
            ->with(['orderItems.product', 'payments'])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy(function($order) {
                // Session ID varsa onu kullan, yoksa masa numarası + updated_at timestamp'i
                return $order->session_id ?? 'table_' . $order->table_number . '_' . $order->updated_at->format('YmdHis');
            })
            ->map(function($tableOrders) {
                // Her session için tek bir "sipariş" objesi oluştur
                $firstOrder = $tableOrders->first();
                
                // İptal edilen siparişler hariç toplam tutarı hesapla
                $totalAmount = $tableOrders->whereNotIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal'])->sum('total');
                $totalPaid = $tableOrders->sum('paid_amount');
                $allOrderItems = $tableOrders->flatMap(function($order) use ($tableOrders) {
                    $items = $order->orderItems;
                    foreach($items as $item) {
                        $item->order_status = $order->status;
                        $item->is_cancelled = $order->isCancelled();
                        $item->is_zafiyat = $order->isZafiyat();
                    }
                    return $items;
                });
                $allPayments = $tableOrders->flatMap(function($order) {
                    return $order->payments;
                });
                
                // Yeni bir obje oluştur (Order modeli değil, stdClass)
                $combinedOrder = new \stdClass();
                $combinedOrder->id = $firstOrder->session_id ?? 'table_' . $firstOrder->table_number . '_' . $firstOrder->updated_at->format('YmdHis');
                $combinedOrder->table_number = $firstOrder->table_number;
                $combinedOrder->customer_name = $firstOrder->customer_name;
                $combinedOrder->status = $firstOrder->status;
                $combinedOrder->payment_status = $firstOrder->payment_status;
                $combinedOrder->total = $totalAmount;
                $combinedOrder->paid_amount = $totalPaid;
                $combinedOrder->created_at = $tableOrders->min('created_at');
                $combinedOrder->updated_at = $tableOrders->max('updated_at');
                $combinedOrder->orderItems = $allOrderItems;
                $combinedOrder->payments = $allPayments;
                $combinedOrder->original_status = $firstOrder->original_status;
                $combinedOrder->session_id = $firstOrder->session_id ?? 'table_' . $firstOrder->table_number . '_' . $firstOrder->updated_at->format('YmdHis');
                // Basit boolean değerler olarak hesapla
                $combinedOrder->isCancelled = $tableOrders->contains(function($order) {
                    return $order->isCancelled();
                });
                
                $combinedOrder->isZafiyat = $tableOrders->contains(function($order) {
                    return $order->isZafiyat();
                });
                
                // Display status'u hesapla
                $cancelledOrder = $tableOrders->first(function($order) {
                    return $order->isCancelled();
                });
                if ($cancelledOrder) {
                    $combinedOrder->displayStatus = $cancelledOrder->getDisplayStatus();
                } else {
                    $zafiyatOrder = $tableOrders->first(function($order) {
                        return $order->isZafiyat();
                    });
                    if ($zafiyatOrder) {
                        $combinedOrder->displayStatus = $zafiyatOrder->getDisplayStatus();
                    } else {
                        $combinedOrder->displayStatus = $tableOrders->first()->getDisplayStatus();
                    }
                }
                
                return $combinedOrder;
            })
            ->values(); // Array'e çevir

        $todayRevenue = $restaurant->getTodayRevenue();
        $todayOrdersCount = $restaurant->getTodayOrdersCount();
        $completedOrdersCount = $recentPayments->count();

        return view('restaurant.cashier', compact(
            'restaurant', 
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
            'order_id' => 'nullable|exists:orders,id',
            'payments' => 'required|array',
            'payments.*.method' => 'required|in:nakit,kart,other',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.note' => 'nullable|string',
        ]);

        // Masa bazlı açık siparişleri al
        $tableOrders = $restaurant->orders()
            ->where('table_number', $request->table_number)
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered', 'zafiyat', 'kitchen_cancelled'])
            ->whereNull('session_id') // Sadece açık masadaki siparişler
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada açık sipariş bulunamadı.'], 404);
        }

        // Toplam kalan ödeme tutarını hesapla (iptal edilen siparişler hariç)
        $totalRemainingAmount = $tableOrders->whereNotIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal'])->sum(function($order) {
            return $order->total - $order->paid_amount;
        });

        $paymentTotal = collect($request->payments)->sum('amount');

        // Ödeme tutarı kalan tutardan fazla olamaz
        if ($paymentTotal > $totalRemainingAmount) {
            return response()->json([
                'success' => false, 
                message => 'Ödeme tutarı kalan tutardan fazla olamaz.'
            ], 400);
        }

        // Ödemeyi kalan tutarlara göre dağıt (iptal edilen siparişler hariç)
        $remainingPayment = $paymentTotal;
        $processedOrders = [];
        $activeOrders = $tableOrders->whereNotIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal']);

        foreach ($activeOrders as $order) {
            if ($remainingPayment <= 0) break;

            $orderRemaining = $order->total - $order->paid_amount;
            if ($orderRemaining <= 0) continue;

            $paymentForThisOrder = min($remainingPayment, $orderRemaining);
            $remainingPayment -= $paymentForThisOrder;

            // Bu sipariş için ödeme kayıtları oluştur
            $this->createOrderPayments($order, $request->payments, $paymentForThisOrder, $user->id);
            
            // Siparişin ödenen tutarını güncelle
            $newPaidAmount = $order->paid_amount + $paymentForThisOrder;
            $paymentStatus = $newPaidAmount >= $order->total ? 'paid' : 'partially_paid';
            
            // Eğer tamamen ödendiyse siparişi tamamla
            $orderStatus = $paymentStatus === 'paid' ? 'delivered' : $order->status;

            $order->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $paymentStatus,
                'status' => $orderStatus
            ]);

            $processedOrders[] = $order->id;
        }

        // Otomatik masa kapatma kontrolü
        $allActiveOrdersPaid = $activeOrders->every(function($order) {
            return $order->paid_amount >= $order->total;
        });

        if ($allActiveOrdersPaid && $activeOrders->isNotEmpty()) {
            // Session ID oluştur (masa kapatma zamanı)
            $sessionId = 'session_' . $request->table_number . '_' . now()->format('Ymd_His');
            
            // Tüm siparişleri kapat
            foreach ($tableOrders as $order) {
                $order->update([
                    'status' => 'delivered',
                    'payment_status' => $order->paid_amount >= $order->total ? 'paid' : 'partially_paid',
                    'session_id' => $sessionId
                ]);
            }
            
            // Masa fişi URL'ini response'a ekle (sadece masa fişi)
            $tableReceiptUrl = route('restaurant.cashier.print-table-receipt', $request->table_number);
        }

        $message = "Masa {$request->table_number} için ₺{$paymentTotal} ödeme alındı!";
        
        if ($allActiveOrdersPaid && $activeOrders->isNotEmpty()) {
            $message .= " Masa otomatik olarak kapatıldı.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'processed_orders' => $processedOrders,
            'total_paid' => $paymentTotal,
            'table_closed' => $allActiveOrdersPaid && $activeOrders->isNotEmpty(),
            'table_receipt_url' => $tableReceiptUrl ?? null
        ]);
    }

    // Sipariş için ödeme kayıtları oluştur
    private function createOrderPayments($order, $payments, $totalAmount, $userId)
    {
        $remainingAmount = $totalAmount;
        
        foreach ($payments as $payment) {
            if ($remainingAmount <= 0) break;
            
            $paymentAmount = min($remainingAmount, $payment['amount']);
            $remainingAmount -= $paymentAmount;
            
            $order->payments()->create([
                'amount' => $paymentAmount,
                'payment_method' => $payment['method'],
                'note' => $payment['note'] ?? null,
                'created_by_user_id' => $userId
            ]);
        }
    }

    // Ödeme silme
    public function deletePayment(Request $request, $paymentId)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $payment = \App\Models\OrderPayment::where('id', $paymentId)
            ->whereHas('order', function($query) use ($restaurant) {
                $query->where('restaurant_id', $restaurant->id);
            })
            ->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Ödeme bulunamadı.'], 404);
        }

        $order = $payment->order;
        $paymentAmount = $payment->amount;

        // Ödemeyi sil
        $payment->delete();

        // Siparişin ödenen tutarını güncelle
        $newPaidAmount = $order->paid_amount - $paymentAmount;
        $paymentStatus = $newPaidAmount >= $order->total ? 'paid' : ($newPaidAmount > 0 ? 'partially_paid' : 'unpaid');

        $order->update([
            'paid_amount' => max(0, $newPaidAmount),
            'payment_status' => $paymentStatus
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödeme başarıyla silindi!'
        ]);
    }

    // Masa kapatma
    public function closeTable(Request $request, $tableNumber)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        $tableOrders = $restaurant->orders()
            ->where('table_number', $tableNumber)
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered', 'zafiyat', 'kitchen_cancelled'])
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereNull('payment_status');
            })
            ->whereNull('session_id') // Sadece açık masadaki siparişler
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada açık sipariş bulunamadı.'], 404);
        }

        // Session ID oluştur (masa kapatma zamanı)
        $sessionId = 'session_' . $tableNumber . '_' . now()->format('Ymd_His');
        
        foreach ($tableOrders as $order) {
            $order->update([
                'status' => 'delivered',
                'payment_status' => $order->paid_amount >= $order->total ? 'paid' : 'partially_paid',
                'session_id' => $sessionId
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Masa {$tableNumber} başarıyla kapatıldı!"
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
            ->whereIn('status', ['pending', 'preparing', 'ready', 'delivered', 'zafiyat', 'kitchen_cancelled'])
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereNull('payment_status');
            })
            ->whereNull('session_id') // Sadece açık masadaki siparişler
            ->with(['orderItems.product', 'createdBy', 'payments'])
            ->orderBy('created_at')
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada açık sipariş bulunamadı.'], 404);
        }

        $totalAmount = $tableOrders->sum('total');
        $totalPaid = $tableOrders->sum('paid_amount');
        $totalRemaining = $totalAmount - $totalPaid;

        return response()->json([
            'success' => true,
            'table_number' => $tableNumber,
            'orders' => $tableOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                    'paid_amount' => (float) $order->paid_amount,
                    'payment_status' => $order->payment_status,
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
                    }),
                    'payments' => $order->payments->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => (float) $payment->amount,
                            'payment_method' => $payment->payment_method,
                            'note' => $payment->note,
                            'created_at' => $payment->created_at->toISOString()
                        ];
                    })
                ];
            }),
            'total_amount' => (float) $totalAmount,
            'total_paid' => (float) $totalPaid,
            'total_remaining' => (float) $totalRemaining,
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
                     ->with(['orderItems.product', 'restaurant', 'payments'])
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Bu siparişe erişim yetkiniz yok.'], 403);
        }

        // Ürün durum bilgilerini ekle
        foreach($order->orderItems as $item) {
            $item->order_status = $order->status;
            $item->is_cancelled = $order->isCancelled();
            $item->is_zafiyat = $order->isZafiyat();
        }

        return view('restaurant.receipt', compact('order', 'restaurant'));
    }

    // Kasa - Masa Fişi Yazdır
    public function printTableReceipt(Request $request, $tableNumber, $sessionId = null)
    {
        $user = Auth::user();
        $restaurant = $this->getUserRestaurant($user);
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
        }

        if ($sessionId) {
            // Session ID'yi parse et
            if (strpos($sessionId, 'table_') === 0) {
                // Kasiyer panelinden gelen session_id formatı: table_MASANUMARASI_TIMESTAMP
                $parts = explode('_', $sessionId);
                if (count($parts) >= 3) {
                    $tableNum = $parts[1];
                    $timestamp = $parts[2];
                    
                    // Bu timestamp'e yakın zamandaki siparişleri bul (10 dakikalık aralık)
                    $targetTime = \Carbon\Carbon::createFromFormat('YmdHis', $timestamp);
                    $tableOrders = $restaurant->orders()
                        ->where('table_number', $tableNum)
                        ->where('updated_at', '>=', $targetTime->copy()->subMinutes(10))
                        ->where('updated_at', '<=', $targetTime->copy()->addMinutes(10))
                        ->with(['orderItems.product', 'payments'])
                        ->orderBy('created_at')
                        ->get();
                } else {
                    $tableOrders = collect();
                }
            } else {
                // Gerçek session_id ile arama
                $tableOrders = $restaurant->orders()
                    ->where('table_number', $tableNumber)
                    ->where('session_id', $sessionId)
                    ->with(['orderItems.product', 'payments'])
                    ->orderBy('created_at')
                    ->get();
            }
        } else {
            // Önce en son kapatılan masayı bul (en son session_id)
            $latestSession = $restaurant->orders()
                ->where('table_number', $tableNumber)
                ->whereNotNull('session_id')
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($latestSession) {
                // En son session_id'ye sahip siparişleri al
                $tableOrders = $restaurant->orders()
                    ->where('table_number', $tableNumber)
                    ->where('session_id', $latestSession->session_id)
                    ->with(['orderItems.product', 'payments'])
                    ->orderBy('created_at')
                    ->get();
            } else {
                // Açık masayı kontrol et
                $tableOrders = $restaurant->orders()
                    ->where('table_number', $tableNumber)
                    ->whereNull('session_id')
                    ->with(['orderItems.product', 'payments'])
                    ->orderBy('created_at')
                    ->get();
            }
        }

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu masada sipariş bulunamadı. Masa: ' . $tableNumber], 404);
        }

        // Toplam hesaplamaları - İptal edilen siparişler hariç
        $totalAmount = $tableOrders->whereNotIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal'])->sum('total');
        $totalPaid = $tableOrders->sum(function($order) {
            return $order->payments->sum('amount');
        });
        $cancelledAmount = $tableOrders->whereIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal'])->sum('total');

        // Her ürün için durum bilgisini ekle
        foreach($tableOrders as $order) {
            foreach($order->orderItems as $item) {
                $item->order_status = $order->status;
                $item->is_cancelled = $order->isCancelled();
                $item->is_zafiyat = $order->isZafiyat();
            }
        }

        return view('restaurant.table_receipt', compact('tableOrders', 'restaurant', 'tableNumber', 'totalAmount', 'totalPaid', 'cancelledAmount'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
                $business = $businesses->first();
                return $business->restaurants()->first();
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
        try {
            $user = Auth::user();
            $restaurant = $this->getUserRestaurant($user);

            if (!$restaurant) {
                return response()->json(['success' => false, 'message' => 'Bu restorana erişim yetkiniz yok.'], 403);
            }

            // İşletmenin paket limitlerini kontrol et
            $business = $restaurant->business;
            if (!$business->hasActiveSubscription()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Masa eklemek için aktif bir paket aboneliğiniz olması gerekiyor.'
                ], 403);
            }

            // Masa sayısı limitini kontrol et
            $maxTables = $business->getFeatureLimit('max_tables');
            $currentTableCount = $restaurant->tables()->count();
            
            if ($maxTables !== null && $maxTables !== 0 && $currentTableCount >= $maxTables) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Maksimum masa sayısına ulaştınız. Daha fazla masa eklemek için paketinizi yükseltmelisiniz.'
                ], 403);
            }

            $request->validate([
                'table_number' => 'required|string|max:20',
                'capacity' => 'nullable|integer|min:1|max:100',
                'location' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:255',
            ]);

            // Aynı masa numarasının olup olmadığını kontrol et (sadece bu restoran için)
            $existingTable = $restaurant->tables()->where('table_number', $request->table_number)->first();
            if ($existingTable) {
                return response()->json(['success' => false, 'message' => 'Bu masa numarası zaten mevcut!'], 400);
            }

            $table = $restaurant->tables()->create([
                'table_number' => $request->table_number,
                'capacity' => $request->capacity ?? 1,
                'location' => $request->location,
                'description' => $request->description,
                'is_active' => true
            ]);

            if (!$table) {
                throw new \Exception('Masa veritabanına eklenemedi');
            }

            return response()->json([
                'success' => true,
                'message' => 'Masa başarıyla eklendi!',
                'table' => $table
            ]);

        } catch (\Exception $e) {
            \Log::error('Masa ekleme hatası: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Masa eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }

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
        $order->updateStatus('kitchen_cancelled', 'Mutfak tarafından iptal edildi');
        return response()->json(['success' => true, 'message' => 'Sipariş mutfak tarafından iptal edildi.']);
    }
}
