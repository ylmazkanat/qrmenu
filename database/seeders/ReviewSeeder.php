<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::first();
        
        if ($restaurant) {
            $reviews = [
                [
                    'rating' => 5,
                    'comment' => 'Harika bir deneyim! Yemekler çok lezzetli ve servis hızlıydı. Kesinlikle tekrar geleceğim.',
                    'customer_name' => 'Ahmet Yılmaz',
                    'customer_email' => 'ahmet@example.com',
                    'is_approved' => true
                ],
                [
                    'rating' => 4,
                    'comment' => 'Güzel bir restoran. Yemekler taze ve lezzetli. Fiyatlar da makul.',
                    'customer_name' => 'Ayşe Demir',
                    'customer_email' => 'ayse@example.com',
                    'is_approved' => true
                ],
                [
                    'rating' => 5,
                    'comment' => 'Mükemmel! Hem yemekler hem de atmosfer harikaydı. Personel çok ilgiliydi.',
                    'customer_name' => 'Mehmet Kaya',
                    'customer_email' => 'mehmet@example.com',
                    'is_approved' => true
                ],
                [
                    'rating' => 3,
                    'comment' => 'Orta seviyede bir deneyim. Yemekler iyiydi ama servis biraz yavaştı.',
                    'customer_name' => 'Fatma Özkan',
                    'customer_email' => 'fatma@example.com',
                    'is_approved' => false
                ],
                [
                    'rating' => 5,
                    'comment' => 'Çok beğendim! Özellikle tatlılar muhteşemdi. Kesinlikle tavsiye ederim.',
                    'customer_name' => 'Ali Veli',
                    'customer_email' => 'ali@example.com',
                    'is_approved' => true
                ]
            ];

            foreach ($reviews as $reviewData) {
                $restaurant->reviews()->create($reviewData);
            }
            
            $this->command->info('Sample reviews created successfully!');
        } else {
            $this->command->error('No restaurant found!');
        }
    }
}
