<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Restaurant;

class CheckRestaurantAccess
{
    public function handle(Request $request, Closure $next)
    {
        $restaurantId = $request->route('restaurant');
        
        if (!$restaurantId) {
            return $next($request);
        }

        $restaurant = Restaurant::find($restaurantId);
        
        if (!$restaurant) {
            abort(404);
        }

        if (!auth()->user()->canAccessRestaurant($restaurant)) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
