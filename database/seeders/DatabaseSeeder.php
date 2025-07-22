<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin kullanıcısı oluştur
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@qrmenu.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // 2. İşletme sahibi oluştur
        $businessOwner = User::create([
            'name' => 'İşletme Sahibi',
            'email' => 'isletme@qrmenu.com',
            'password' => 'password',
            'role' => 'business_owner',
        ]);

        // 3. İşletme oluştur
        $business = Business::create([
            'owner_id' => $businessOwner->id,
            'name' => 'Test İşletmesi',
            'slug' => 'test-isletmesi',
            'description' => 'Bu bir test işletmesidir. Birden fazla restoranı vardır.',
            'phone' => '+90 555 123 4567',
            'address' => 'Test Mahallesi, Test Caddesi No:1, İstanbul',
            'tax_number' => '1234567890',
            'email' => 'info@testisletmesi.com',
            'plan' => 'basic',
            'is_active' => true,
        ]);

        // 4. Restoran yöneticisi oluştur
        $restaurantManager = User::create([
            'name' => 'Restoran Müdürü',
            'email' => 'mudur@restaurant.com',
            'password' => 'password',
            'role' => 'restaurant_manager',
        ]);

        // 5. Restoran çalışanları oluştur
        $waiter1 = User::create([
            'name' => 'Garson Ali',
            'email' => 'garson1@restaurant.com',
            'password' => 'password',
            'role' => 'waiter',
        ]);

        $waiter2 = User::create([
            'name' => 'Garson Ayşe',
            'email' => 'garson2@restaurant.com',
            'password' => 'password',
            'role' => 'waiter',
        ]);

        $kitchen1 = User::create([
            'name' => 'Aşçı Mehmet',
            'email' => 'asci1@restaurant.com',
            'password' => 'password',
            'role' => 'kitchen',
        ]);

        $cashier1 = User::create([
            'name' => 'Kasiyer Fatma',
            'email' => 'kasiyer1@restaurant.com',
            'password' => 'password',
            'role' => 'cashier',
        ]);

        // 6. Test restoranı oluştur
        $restaurant = Restaurant::create([
            'business_id' => $business->id,
            'restaurant_manager_id' => $restaurantManager->id,
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'description' => 'Bu bir test restoranıdır. Lezzetli yemekler ve kaliteli hizmet.',
            'phone' => '+90 555 987 6543',
            'address' => 'Restoran Mahallesi, Lezzet Caddesi No:5, İstanbul',
            'table_count' => 20,
            'working_hours' => [
                'monday' => ['open' => '09:00', 'close' => '23:00'],
                'tuesday' => ['open' => '09:00', 'close' => '23:00'],
                'wednesday' => ['open' => '09:00', 'close' => '23:00'],
                'thursday' => ['open' => '09:00', 'close' => '23:00'],
                'friday' => ['open' => '09:00', 'close' => '24:00'],
                'saturday' => ['open' => '09:00', 'close' => '24:00'],
                'sunday' => ['open' => '10:00', 'close' => '22:00'],
            ],
            'is_active' => true,
        ]);

        // 7. Restoran çalışanlarını restorana ata
        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $restaurantManager->id,
            'role' => 'restaurant_manager',
            'is_active' => true,
        ]);

        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $waiter1->id,
            'role' => 'waiter',
            'pin_code' => '1234',
            'is_active' => true,
        ]);

        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $waiter2->id,
            'role' => 'waiter',
            'pin_code' => '5678',
            'is_active' => true,
        ]);

        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $kitchen1->id,
            'role' => 'kitchen',
            'pin_code' => '9999',
            'is_active' => true,
        ]);

        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $cashier1->id,
            'role' => 'cashier',
            'pin_code' => '0000',
            'is_active' => true,
        ]);

        // 8. İkinci test restoranı oluştur
        $restaurant2 = Restaurant::create([
            'business_id' => $business->id,
            'restaurant_manager_id' => null,
            'name' => 'Pizza House',
            'slug' => 'pizza-house',
            'description' => 'En lezzetli pizzalar burada!',
            'phone' => '+90 555 111 2233',
            'address' => 'Pizza Mahallesi, İtalyan Caddesi No:10, İstanbul',
            'table_count' => 15,
            'is_active' => true,
        ]);

        // 9. Kategoriler oluştur (Test Restaurant için)
        $categories = [
            ['name' => 'Ana Yemekler', 'sort_order' => 1],
            ['name' => 'İçecekler', 'sort_order' => 2],
            ['name' => 'Tatlılar', 'sort_order' => 3],
            ['name' => 'Başlangıçlar', 'sort_order' => 4],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['restaurant_id'] = $restaurant->id;
            Category::create($categoryData);
        }

        // 10. Pizza House kategorileri
        $pizzaCategories = [
            ['name' => 'Pizzalar', 'sort_order' => 1],
            ['name' => 'İçecekler', 'sort_order' => 2],
            ['name' => 'Salata', 'sort_order' => 3],
        ];

        foreach ($pizzaCategories as $categoryData) {
            $categoryData['restaurant_id'] = $restaurant2->id;
            Category::create($categoryData);
        }

        // 11. Ürünler oluştur (Test Restaurant)
        $products = [
            // Ana Yemekler
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => 1,
                'name' => 'Köfte',
                'description' => 'Özel karışım dana etiyle hazırlanan ev yapımı köftelerimiz, geleneksel Türk mutfağının en seçkin lezzetlerinden biridir. Yoğrulmuş köftelerimiz mangal ateşinde pişirilerek servise sunulur. Yanında çıtır çıtır patates kızartması, taze mevsim salatası ve özel soslarımız ile birlikte servis edilir. Köftelerimiz tamamen doğal malzemeler kullanılarak, hiçbir katkı maddesi kullanılmadan hazırlanır.',
                'price' => 45.00,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => 1,
                'name' => 'Tavuk Şiş',
                'description' => 'Özel baharat karışımımızla marine edilmiş tavuk göğsü etinden hazırlanan şişlerimiz, açık ateşte usta ellerle pişirilir. Marine işlemi minimum 24 saat sürer ve böylece etin tam kıvamını alması sağlanır. Yanında tereyağlı pilav, közlenmiş sebzeler ve taze mevsim salatası ile servis edilir. Tavuklarımız çiftlik tavuğu olup, doğal beslenmiş tavuklardan seçilir.',
                'price' => 55.00,
                'sort_order' => 2,
            ],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => 1,
                'name' => 'Karışık Izgara',
                'description' => 'Restoran şefimizin özel karışık ızgara tabağı; dana köfte, tavuk şiş ve kuzu şiş üçlüsünden oluşur. Her bir et çeşidi kendine özgü baharat karışımları ile marine edilir ve açık ateşte mükemmel pişirme tekniği ile hazırlanır. Yanında bulgur pilavı, közlenmiş domates ve biber, taze soğan, turşu çeşitleri ve özel acı ezme ile zengin bir sunum yapılır. 2-3 kişi için idealdir.',
                'price' => 85.00,
                'sort_order' => 3,
            ],
            
            // İçecekler
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => 2,
                'name' => 'Çay',
                'description' => 'Rize yöresinden özenle seçilmiş birinci kalite çay yaprakları ile demlenen geleneksel Türk çayımız. Çayımız özel cam demlikte, geleneksel yöntemlerle demlenir ve sürekli taze tutulur. İnce belli bardaklarımızda, şeker ile birlikte servis edilir. Türk misafirperverliğinin en güzel sembollerinden biri olan çayımız, yemek sonrası mükemmel bir seçimdir.',
                'price' => 5.00,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => 2,
                'name' => 'Ayran',
                'description' => 'Taze yoğurt ve saf su ile hazırlanan ev yapımı ayranımız, hiçbir koruyucu madde içermez. Geleneksel yöntemlerle çırpılan ayranımız, yemeklerinizin yanında mükemmel bir serinlik sağlar.',
                'price' => 8.00,
                'sort_order' => 2,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // 12. Pizza House ürünleri
        $pizzaProducts = [
            [
                'restaurant_id' => $restaurant2->id,
                'category_id' => 5, // Pizzalar kategorisi
                'name' => 'Margherita Pizza',
                'description' => 'Klasik Margherita pizza - mozarella, domates sosu, fesleğen',
                'price' => 35.00,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => $restaurant2->id,
                'category_id' => 5,
                'name' => 'Pepperoni Pizza',
                'description' => 'Pepperoni, mozarella, domates sosu',
                'price' => 42.00,
                'sort_order' => 2,
            ],
        ];

        foreach ($pizzaProducts as $productData) {
            Product::create($productData);
        }

        echo "✅ Yeni sistem için test verileri başarıyla oluşturuldu!\n";
        echo "\n🎯 Test Hesapları:\n";
        echo "👑 Admin: admin@qrmenu.com (password: password)\n";
        echo "🏢 İşletme Sahibi: isletme@qrmenu.com (password: password)\n";
        echo "🍽️ Restoran Müdürü: mudur@restaurant.com (password: password)\n";
        echo "👤 Garson 1: garson1@restaurant.com (password: password)\n";
        echo "👤 Garson 2: garson2@restaurant.com (password: password)\n";
        echo "👨‍🍳 Aşçı: asci1@restaurant.com (password: password)\n";
        echo "💰 Kasiyer: kasiyer1@restaurant.com (password: password)\n";
        echo "\n🍽️ Test Menüleri:\n";
        echo "📍 Test Restaurant: http://localhost:8000/menu/test-restaurant\n";
        echo "📍 Pizza House: http://localhost:8000/menu/pizza-house\n";
        echo "\n🎯 Sistem Yapısı:\n";
        echo "1️⃣ Admin Panel → Tüm işletmeleri yönetir\n";
        echo "2️⃣ İşletme Panel → Restoranları yönetir  \n";
        echo "3️⃣ Restoran Panel → Günlük operasyonlar (garson, mutfak, kasa)\n";
    }
}
