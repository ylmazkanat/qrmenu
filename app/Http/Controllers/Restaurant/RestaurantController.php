<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Restaurant dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $restaurants = $user->restaurants()->with(['categories', 'products', 'orders'])->get();
        
        return view('restaurant.dashboard', compact('restaurants'));
    }

    /**
     * Show create restaurant form
     */
    public function create()
    {
        return view('restaurant.create');
    }

    /**
     * Store a new restaurant
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $slug = Str::slug($request->name);
        
        // Check if slug exists
        $counter = 1;
        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . $counter;
            $counter++;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Restaurant::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        return redirect()->route('restaurant.dashboard')->with('success', 'Restoran başarıyla oluşturuldu.');
    }

    /**
     * Show edit restaurant form
     */
    public function edit(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        return view('restaurant.edit', compact('restaurant'));
    }

    /**
     * Update restaurant
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'phone', 'address']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $restaurant->update($data);

        return redirect()->route('restaurant.dashboard')->with('success', 'Restoran bilgileri güncellendi.');
    }

    /**
     * Delete restaurant
     */
    public function destroy(Restaurant $restaurant)
    {
        $this->authorize('delete', $restaurant);

        if ($restaurant->logo) {
            Storage::disk('public')->delete($restaurant->logo);
        }

        $restaurant->delete();

        return redirect()->route('restaurant.dashboard')->with('success', 'Restoran silindi.');
    }

    /**
     * Show restaurant analytics
     */
    public function analytics(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);

        $totalOrders = $restaurant->orders()->count();
        $totalRevenue = $restaurant->orders()->sum('total');
        $todayOrders = $restaurant->orders()->whereDate('created_at', today())->count();
        $todayRevenue = $restaurant->orders()->whereDate('created_at', today())->sum('total');

        return view('restaurant.analytics', compact('restaurant', 'totalOrders', 'totalRevenue', 'todayOrders', 'todayRevenue'));
    }
}
