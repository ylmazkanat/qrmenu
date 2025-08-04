<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Restaurant\RestaurantPanelController;
use App\Http\Controllers\Menu\MenuController;
use Illuminate\Support\Facades\Route;

// Ana sayfa - Role göre yönlendirme
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isBusinessOwner()) {
            return redirect()->route('business.dashboard');
        } elseif ($user->isRestaurantManager()) {
            return redirect()->route('restaurant.dashboard');
        } elseif ($user->isCashier()) {
            return redirect()->route('restaurant.cashier');
        } elseif ($user->isWaiter()) {
            return redirect()->route('restaurant.waiter');
        } elseif ($user->isKitchen()) {
            return redirect()->route('restaurant.kitchen');
        }
    }
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔥 1. ADMIN PANEL (Yazılım Yöneticisi)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/businesses', [AdminController::class, 'businesses'])->name('businesses');
    Route::get('/businesses/{business}', [AdminController::class, 'showBusiness'])->name('businesses.show');
    Route::post('/businesses/{business}/toggle-status', [AdminController::class, 'toggleBusinessStatus'])->name('businesses.toggle-status');
    Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('restaurants');
    Route::post('/restaurants/{restaurant}/toggle-status', [AdminController::class, 'toggleRestaurantStatus'])->name('restaurants.toggle-status');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/impersonate', [AdminController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
});

// 🏢 2. BUSINESS PANEL (İşletme Sahibi)
Route::middleware(['auth', 'role:business_owner'])->prefix('business')->name('business.')->group(function () {
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [BusinessController::class, 'create'])->name('create');
    Route::post('/store', [BusinessController::class, 'store'])->name('store');
    Route::get('/restaurants', [BusinessController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/create', [BusinessController::class, 'createRestaurant'])->name('restaurants.create');
    Route::post('/restaurants/store', [BusinessController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::get('/restaurants/{restaurant}', [BusinessController::class, 'showRestaurant'])->name('restaurants.show');
    Route::get('/restaurants/{restaurant}/edit', [BusinessController::class, 'editRestaurant'])->name('restaurants.edit');
    Route::put('/restaurants/{restaurant}', [BusinessController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::get('/staff', [BusinessController::class, 'staffManagement'])->name('staff');
    Route::post('/staff/store', [BusinessController::class, 'storeStaff'])->name('staff.store');
    Route::post('/staff/{staff}/toggle-status', [BusinessController::class, 'toggleStaffStatus'])->name('staff.toggle-status');
    Route::delete('/staff/{staff}', [BusinessController::class, 'deleteStaff'])->name('staff.delete');
    Route::get('/analytics', [BusinessController::class, 'analytics'])->name('analytics');
    
    // Değerlendirme yönetimi
    Route::get('/restaurants/{restaurant}/reviews', [BusinessController::class, 'reviews'])->name('restaurants.reviews');
    Route::post('/reviews/{review}/approve', [BusinessController::class, 'approveReview'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [BusinessController::class, 'deleteReview'])->name('reviews.delete');
});

// 🍽️ 3. RESTAURANT PANEL (Restoran Operasyonları) - Basitleştirilmiş
Route::middleware(['auth'])->prefix('restaurant')->name('restaurant.')->group(function () {
    
    // Tüm restaurant rolleri için ortak dashboard
    Route::get('/dashboard', [RestaurantPanelController::class, 'dashboard'])->name('dashboard');
    
    // Garson Paneli - restaurant_manager veya waiter
    Route::get('/waiter', [RestaurantPanelController::class, 'waiter'])->name('waiter');
    Route::post('/orders', [RestaurantPanelController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders/{order}/deliver', [RestaurantPanelController::class, 'markAsDelivered'])->name('orders.deliver');
    Route::post('/orders/{order}/cancel', [RestaurantPanelController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{order}/update-table', [RestaurantPanelController::class, 'updateOrderTable'])->name('orders.update-table');
    
    // Mutfak Paneli - restaurant_manager veya kitchen
    Route::get('/kitchen', [RestaurantPanelController::class, 'kitchen'])->name('kitchen');
    // Menü Yönetimi (Kategori & Ürün CRUD)
    Route::get('/menu-management', [RestaurantPanelController::class, 'menuManagement'])->name('menu.management');

    // Müşteri Menü Route'ları
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/{slug}', [MenuController::class, 'show'])->name('show');
        Route::get('/{slug}/category', [MenuController::class, 'showCategories'])->name('categories');
    });
    
    // Kategori İşlemleri
    Route::post('/categories', [RestaurantPanelController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [RestaurantPanelController::class, 'getCategory'])->name('categories.get');
    Route::put('/categories/{category}', [RestaurantPanelController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [RestaurantPanelController::class, 'deleteCategory'])->name('categories.delete');
    
    // Ürün İşlemleri
    Route::post('/products', [RestaurantPanelController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}', [RestaurantPanelController::class, 'getProduct'])->name('products.get');
    Route::put('/products/{product}', [RestaurantPanelController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [RestaurantPanelController::class, 'deleteProduct'])->name('products.delete');
    
    // Masa İşlemleri
    Route::post('/tables', [RestaurantPanelController::class, 'storeTables'])->name('tables.store');
    Route::get('/tables/{table}', [RestaurantPanelController::class, 'getTable'])->name('tables.get');
    Route::put('/tables/{table}', [RestaurantPanelController::class, 'updateTable'])->name('tables.update');
    Route::delete('/tables/{table}', [RestaurantPanelController::class, 'deleteTable'])->name('tables.delete');
    
    // Sipariş Ayarları
    Route::post('/order-settings', [RestaurantPanelController::class, 'storeOrderSettings'])->name('order-settings.store');
    
    Route::post('/kitchen/{order}/start-preparing', [RestaurantPanelController::class, 'startPreparing'])->name('kitchen.start-preparing');
    Route::post('/kitchen/{order}/mark-ready', [RestaurantPanelController::class, 'markAsReady'])->name('kitchen.mark-ready');
    Route::post('/kitchen/{order}/zafiyat', [RestaurantPanelController::class, 'markAsZafiyat'])->name('kitchen.mark-zafiyat');
    Route::post('/kitchen/{order}/cancel', [RestaurantPanelController::class, 'kitchenCancelOrder'])->name('kitchen.cancel-order');
    
    // Kasa Paneli - restaurant_manager veya cashier
    Route::get('/cashier', [RestaurantPanelController::class, 'cashier'])->name('cashier');
    Route::post('/cashier/process-table-payment', [RestaurantPanelController::class, 'processTablePayment'])->name('cashier.process-table-payment');
    Route::get('/cashier/endofday-pdf', [RestaurantPanelController::class, 'endOfDayPdf'])->name('cashier.endofday.pdf');
    Route::get('/tables/{tableNumber}/details', [RestaurantPanelController::class, 'getTableDetails'])->name('tables.details');
    Route::get('/cashier/{order}/print-receipt', [RestaurantPanelController::class, 'printReceipt'])->name('cashier.print-receipt');
    
    // Ortak API'ler (tüm restoran çalışanları)
    Route::get('/api/orders/updates', [RestaurantPanelController::class, 'getOrderUpdates'])->name('api.orders.updates');
});

// 📱 4. PUBLIC MENU ROUTES (Müşteriler)
Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/{slug}', [MenuController::class, 'show'])->name('show');
    Route::get('/{slug}/categories', [MenuController::class, 'showCategories'])->name('categories');
    Route::post('/cart/add', [MenuController::class, 'addToCart'])->name('cart.add');
    Route::get('/{slug}/cart', [MenuController::class, 'cart'])->name('cart');
    Route::post('/cart/update', [MenuController::class, 'updateCart'])->name('cart.update');
    Route::post('/{slug}/order', [MenuController::class, 'placeOrder'])->name('order.place');
    Route::get('/{slug}/order/{order}/success', [MenuController::class, 'orderSuccess'])->name('order-success');
    
    // Değerlendirme route'ları
    Route::post('/{restaurant}/review', [MenuController::class, 'storeReview'])->name('review.store');
});

// 🌐 5. API ROUTES (Mobile App)
Route::prefix('api/v1')->name('api.')->group(function () {
    Route::get('/restaurants/{slug}/menu', [MenuController::class, 'getMenuApi']);
    Route::post('/restaurants/{slug}/orders', [MenuController::class, 'placeOrderApi']);
    Route::get('/orders/{order}/status', [MenuController::class, 'getOrderStatusApi']);
});

// 🔗 6. CUSTOM DOMAIN SUPPORT
Route::domain('{domain}')->group(function () {
    Route::get('/', [MenuController::class, 'showByDomain'])->name('menu.custom-domain');
});

// 🔄 7. LEGACY REDIRECTS
Route::get('/dashboard', function () {
    return redirect('/');
});

Route::get('/restaurant', function () {
    return redirect('/restaurant/dashboard');
});

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

Route::get('/business', function () {
    return redirect('/business/dashboard');
});

// Menü (Müşteri) API
Route::get('/api/menu/{slug}/active-orders', [\App\Http\Controllers\Menu\MenuController::class, 'activeOrders']);
Route::post('/api/menu/{slug}/orders/{order}/cancel', [\App\Http\Controllers\Menu\MenuController::class, 'cancelOrderFromCustomer']);
