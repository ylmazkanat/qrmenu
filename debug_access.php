<?php

require_once 'vendor/autoload.php';

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Kullanıcı ve restaurant bilgilerini al
$user = App\Models\User::find(2);
$restaurant = App\Models\Restaurant::with('business')->find(1);

if (!$user) {
    echo "User not found!\n";
    exit;
}

if (!$restaurant) {
    echo "Restaurant not found!\n";
    exit;
}

echo "=== USER INFO ===\n";
echo "ID: " . $user->id . "\n";
echo "Role: " . $user->role . "\n";
echo "Is Admin: " . ($user->isAdmin() ? 'true' : 'false') . "\n";
echo "Is Business Owner: " . ($user->isBusinessOwner() ? 'true' : 'false') . "\n";

echo "\n=== RESTAURANT INFO ===\n";
echo "ID: " . $restaurant->id . "\n";
echo "Name: " . $restaurant->name . "\n";
echo "Business ID: " . $restaurant->business_id . "\n";
echo "User ID (legacy): " . ($restaurant->user_id ?? 'null') . "\n";
echo "Business Owner ID: " . ($restaurant->business ? $restaurant->business->owner_id : 'no_business') . "\n";

echo "\n=== ACCESS CHECKS ===\n";

// Kullanıcının sahip olduğu business'ları kontrol et
$userBusinesses = $user->getActiveBusinesses();
echo "User Active Businesses: " . $userBusinesses->pluck('id')->toJson() . "\n";

// Kullanıcının staff olduğu restaurant'ları kontrol et
$staffRestaurants = $user->restaurantStaff()->where('is_active', true)->get();
echo "User Staff Restaurants: " . $staffRestaurants->pluck('restaurant_id')->toJson() . "\n";

// canAccessRestaurant metodunu adım adım kontrol et
echo "\n=== DETAILED ACCESS CHECK ===\n";

// 1. Admin kontrolü
if ($user->isAdmin()) {
    echo "✓ User is admin - ACCESS GRANTED\n";
} else {
    echo "✗ User is not admin\n";
    
    // 2. Business owner kontrolü
    if (!is_null($restaurant->business_id)) {
        echo "Restaurant has business_id: " . $restaurant->business_id . "\n";
        
        if ($user->isBusinessOwner()) {
            echo "User is business owner\n";
            $business = $restaurant->business;
            if ($business && $business->owner_id === $user->id) {
                echo "✓ User owns the business - ACCESS GRANTED\n";
            } else {
                echo "✗ User does not own the business (Business owner: " . ($business ? $business->owner_id : 'null') . ")\n";
            }
        } else {
            echo "✗ User is not a business owner\n";
        }
    } else {
        echo "Restaurant has no business_id\n";
    }
    
    // 3. Legacy user_id kontrolü
    if (!is_null($restaurant->user_id) && $restaurant->user_id === $user->id) {
        echo "✓ User owns restaurant (legacy) - ACCESS GRANTED\n";
    } else {
        echo "✗ User does not own restaurant (legacy)\n";
    }
    
    // 4. Staff kontrolü
    $isStaff = $user->restaurantStaff()
        ->where('restaurant_id', $restaurant->id)
        ->where('is_active', true)
        ->exists();
    
    if ($isStaff) {
        echo "✓ User is active staff - ACCESS GRANTED\n";
    } else {
        echo "✗ User is not active staff\n";
    }
}

echo "\n=== FINAL RESULT ===\n";
echo "Can Access Restaurant: " . ($user->canAccessRestaurant($restaurant) ? 'true' : 'false') . "\n";