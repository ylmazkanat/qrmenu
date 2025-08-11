<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ucuz Paket
        $basicPackage = Package::create([
            'name' => 'Ucuz Paket',
            'slug' => 'ucuz-paket',
            'description' => 'Küçük işletmeler için ideal başlangıç paketi',
            'price' => 29.99,
            'billing_cycle' => 'monthly',
            'is_active' => true,
            'is_popular' => false,
            'sort_order' => 1,
        ]);

        // Standart Paket
        $standardPackage = Package::create([
            'name' => 'Standart Paket',
            'slug' => 'standart-paket',
            'description' => 'Orta ölçekli işletmeler için gelişmiş özellikler',
            'price' => 59.99,
            'billing_cycle' => 'monthly',
            'is_active' => true,
            'is_popular' => true,
            'sort_order' => 2,
        ]);

        // Sınırsız Paket
        $unlimitedPackage = Package::create([
            'name' => 'Sınırsız Paket',
            'slug' => 'sinirsiz-paket',
            'description' => 'Büyük işletmeler için tüm özellikler sınırsız',
            'price' => 99.99,
            'billing_cycle' => 'monthly',
            'is_active' => true,
            'is_popular' => false,
            'sort_order' => 3,
        ]);

        // Ucuz Paket Özellikleri
        $basicFeatures = [
            ['feature_key' => 'max_restaurants', 'feature_name' => 'Maksimum Restoran Sayısı', 'limit_value' => 1],
            ['feature_key' => 'max_managers', 'feature_name' => 'Maksimum Müdür Hesabı', 'limit_value' => 1],
            ['feature_key' => 'max_staff', 'feature_name' => 'Maksimum Çalışan Sayısı', 'limit_value' => 5],
            ['feature_key' => 'max_products', 'feature_name' => 'Restoran Maksimum Ürün Limiti', 'limit_value' => 50],
            ['feature_key' => 'max_categories', 'feature_name' => 'Restoran Maksimum Kategori Limiti', 'limit_value' => 5],
            ['feature_key' => 'custom_domain', 'feature_name' => 'Özel Domain', 'limit_value' => 0],
            ['feature_key' => 'multi_language', 'feature_name' => 'Çoklu Dil Desteği', 'limit_value' => 0],
            ['feature_key' => 'customer_reviews', 'feature_name' => 'Müşteri Değerlendirmeleri', 'limit_value' => 0],
            ['feature_key' => 'loyalty_program', 'feature_name' => 'Sadakat Programı (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
            ['feature_key' => 'api_access', 'feature_name' => 'API Erişimi (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
        ];

        // Standart Paket Özellikleri
        $standardFeatures = [
            ['feature_key' => 'max_restaurants', 'feature_name' => 'Maksimum Restoran Sayısı', 'limit_value' => 3],
            ['feature_key' => 'max_managers', 'feature_name' => 'Maksimum Müdür Hesabı', 'limit_value' => 3],
            ['feature_key' => 'max_staff', 'feature_name' => 'Maksimum Çalışan Sayısı', 'limit_value' => 15],
            ['feature_key' => 'max_products', 'feature_name' => 'Restoran Maksimum Ürün Limiti', 'limit_value' => 200],
            ['feature_key' => 'max_categories', 'feature_name' => 'Restoran Maksimum Kategori Limiti', 'limit_value' => 15],
            ['feature_key' => 'custom_domain', 'feature_name' => 'Özel Domain', 'limit_value' => 1],
            ['feature_key' => 'multi_language', 'feature_name' => 'Çoklu Dil Desteği', 'limit_value' => 1],
            ['feature_key' => 'customer_reviews', 'feature_name' => 'Müşteri Değerlendirmeleri', 'limit_value' => 1],
            ['feature_key' => 'loyalty_program', 'feature_name' => 'Sadakat Programı (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
            ['feature_key' => 'api_access', 'feature_name' => 'API Erişimi (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
        ];

        // Sınırsız Paket Özellikleri
        $unlimitedFeatures = [
            ['feature_key' => 'max_restaurants', 'feature_name' => 'Maksimum Restoran Sayısı', 'limit_value' => null],
            ['feature_key' => 'max_managers', 'feature_name' => 'Maksimum Müdür Hesabı', 'limit_value' => null],
            ['feature_key' => 'max_staff', 'feature_name' => 'Maksimum Çalışan Sayısı', 'limit_value' => null],
            ['feature_key' => 'max_products', 'feature_name' => 'Restoran Maksimum Ürün Limiti', 'limit_value' => null],
            ['feature_key' => 'max_categories', 'feature_name' => 'Restoran Maksimum Kategori Limiti', 'limit_value' => null],
            ['feature_key' => 'custom_domain', 'feature_name' => 'Özel Domain', 'limit_value' => 1],
            ['feature_key' => 'multi_language', 'feature_name' => 'Çoklu Dil Desteği', 'limit_value' => 1],
            ['feature_key' => 'customer_reviews', 'feature_name' => 'Müşteri Değerlendirmeleri', 'limit_value' => 1],
            ['feature_key' => 'loyalty_program', 'feature_name' => 'Sadakat Programı (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
            ['feature_key' => 'api_access', 'feature_name' => 'API Erişimi (Yakında)', 'limit_value' => 0, 'is_coming_soon' => true],
        ];

        // Özellikleri ekle
        foreach ($basicFeatures as $index => $feature) {
            PackageFeature::create([
                'package_id' => $basicPackage->id,
                'feature_key' => $feature['feature_key'],
                'feature_name' => $feature['feature_name'],
                'description' => $feature['feature_name'] . ' özelliği',
                'limit_value' => $feature['limit_value'],
                'is_enabled' => true,
                'sort_order' => $index + 1,
                'is_coming_soon' => $feature['is_coming_soon'] ?? false,
            ]);
        }

        foreach ($standardFeatures as $index => $feature) {
            PackageFeature::create([
                'package_id' => $standardPackage->id,
                'feature_key' => $feature['feature_key'],
                'feature_name' => $feature['feature_name'],
                'description' => $feature['feature_name'] . ' özelliği',
                'limit_value' => $feature['limit_value'],
                'is_enabled' => true,
                'sort_order' => $index + 1,
                'is_coming_soon' => $feature['is_coming_soon'] ?? false,
            ]);
        }

        foreach ($unlimitedFeatures as $index => $feature) {
            PackageFeature::create([
                'package_id' => $unlimitedPackage->id,
                'feature_key' => $feature['feature_key'],
                'feature_name' => $feature['feature_name'],
                'description' => $feature['feature_name'] . ' özelliği',
                'limit_value' => $feature['limit_value'],
                'is_enabled' => true,
                'sort_order' => $index + 1,
                'is_coming_soon' => $feature['is_coming_soon'] ?? false,
            ]);
        }
    }
}
