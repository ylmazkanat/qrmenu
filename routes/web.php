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
});

// 🍽️ 3. RESTAURANT PANEL (Restoran Operasyonları) - Basitleştirilmiş
Route::middleware(['auth'])->prefix('restaurant')->name('restaurant.')->group(function () {
    
    // Tüm restaurant rolleri için ortak dashboard
    Route::get('/dashboard', [RestaurantPanelController::class, 'dashboard'])->name('dashboard');
    
    // Garson Paneli - restaurant_manager veya waiter
    Route::get('/waiter', [RestaurantPanelController::class, 'waiter'])->name('waiter');
    Route::post('/orders', [RestaurantPanelController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders/{order}/deliver', [RestaurantPanelController::class, 'markAsDelivered'])->name('orders.deliver');
    
    // Mutfak Paneli - restaurant_manager veya kitchen
    Route::get('/kitchen', [RestaurantPanelController::class, 'kitchen'])->name('kitchen');
    Route::post('/kitchen/{order}/start-preparing', [RestaurantPanelController::class, 'startPreparing'])->name('kitchen.start-preparing');
    Route::post('/kitchen/{order}/mark-ready', [RestaurantPanelController::class, 'markAsReady'])->name('kitchen.mark-ready');
    
    // Kasa Paneli - restaurant_manager veya cashier
    Route::get('/cashier', [RestaurantPanelController::class, 'cashier'])->name('cashier');
    Route::post('/cashier/{order}/process-payment', [RestaurantPanelController::class, 'processPayment'])->name('cashier.process-payment');
    Route::get('/cashier/{order}/print-receipt', [RestaurantPanelController::class, 'printReceipt'])->name('cashier.print-receipt');
    
    // Ortak API'ler (tüm restoran çalışanları)
    Route::get('/api/orders/updates', [RestaurantPanelController::class, 'getOrderUpdates'])->name('api.orders.updates');
});

// 📱 4. PUBLIC MENU ROUTES (Müşteriler)
Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/{slug}', [MenuController::class, 'show'])->name('show');
    Route::post('/cart/add', [MenuController::class, 'addToCart'])->name('cart.add');
    Route::get('/{slug}/cart', [MenuController::class, 'cart'])->name('cart');
    Route::post('/cart/update', [MenuController::class, 'updateCart'])->name('cart.update');
    Route::post('/{slug}/order', [MenuController::class, 'placeOrder'])->name('order.place');
    Route::get('/{slug}/order/{order}/success', [MenuController::class, 'orderSuccess'])->name('order-success');
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
