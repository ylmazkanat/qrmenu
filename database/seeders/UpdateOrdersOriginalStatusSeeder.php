<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class UpdateOrdersOriginalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mevcut siparişlerin original_status alanını doldur
        $orders = Order::whereNull('original_status')->get();
        
        foreach ($orders as $order) {
            $order->original_status = $order->status;
            $order->status_changed_at = $order->updated_at;
            $order->status_changed_by = 'Sistem (Güncelleme)';
            $order->status_change_reason = 'Mevcut siparişler için otomatik güncelleme';
            $order->save();
        }
        
        $this->command->info('Toplam ' . $orders->count() . ' sipariş güncellendi.');
    }
}
