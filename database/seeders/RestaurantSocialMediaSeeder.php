<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::first();
        
        if ($restaurant) {
            $restaurant->update([
                'email' => 'info@restaurant.com',
                'website' => 'https://restaurant.com',
                'facebook' => 'https://facebook.com/restaurant',
                'instagram' => 'https://instagram.com/restaurant',
                'twitter' => 'https://twitter.com/restaurant',
                'youtube' => 'https://youtube.com/restaurant',
                'linkedin' => 'https://linkedin.com/company/restaurant',
                'whatsapp' => '+905551234567',
                'working_hours_text' => "Pazartesi - Cuma: 09:00 - 22:00\nCumartesi - Pazar: 10:00 - 23:00"
            ]);
            
            $this->command->info('Restaurant social media data updated successfully!');
        } else {
            $this->command->error('No restaurant found!');
        }
    }
}
