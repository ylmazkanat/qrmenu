<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\DeletedRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    // ... your existing methods ...

    public function delete(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'confirm_name' => 'required|same:' . $restaurant->name
        ]);

        try {
            DB::beginTransaction();

            // Collect related data
            $relatedData = [
                'categories' => $restaurant->categories()->get()->toArray(),
                'products' => $restaurant->products()->get()->toArray(),
                'tables' => $restaurant->tables()->get()->toArray(),
                'staff' => $restaurant->staff()->get()->toArray(),
                'orders' => $restaurant->orders()->get()->toArray(),
                'reviews' => $restaurant->reviews()->get()->toArray(),
            ];

            // Create deleted restaurant record
            DeletedRestaurant::create([
                'original_id' => $restaurant->id,
                'business_id' => $restaurant->business_id,
                'name' => $restaurant->name,
                'slug' => $restaurant->slug,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'email' => $restaurant->email,
                'description' => $restaurant->description,
                'currency' => $restaurant->currency,
                'timezone' => $restaurant->timezone,
                'logo' => $restaurant->logo,
                'cover' => $restaurant->cover,
                'is_featured' => $restaurant->is_featured,
                'is_active' => $restaurant->is_active,
                'settings' => $restaurant->settings,
                'deleted_related_data' => $relatedData,
                'deleted_at' => now(),
            ]);

            // Delete related records
            $restaurant->categories()->delete();
            $restaurant->products()->delete();
            $restaurant->tables()->delete();
            $restaurant->staff()->delete();
            $restaurant->orders()->delete();
            $restaurant->reviews()->delete();

            // Delete the restaurant
            $restaurant->delete();

            DB::commit();

            return redirect()
                ->route('business.restaurants.index')
                ->with('success', 'Restoran başarıyla silindi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Restoran silinirken bir hata oluştu.');
        }
    }
}
