<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\RestaurantStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class BusinessController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $businesses = $user->getActiveBusinesses();
        
        if ($businesses->isEmpty()) {
            return redirect()->route('business.create');
        }

        $business = $businesses->first(); // İlk işletmeyi göster
        
        $stats = [
            'total_restaurants' => $business->restaurants()->count(),
            'active_restaurants' => $business->restaurants()->where('is_active', true)->count(),
            'total_products' => $business->total_products,
            'total_orders' => $business->total_orders,
            'today_orders' => $business->restaurants()->get()->sum(function($restaurant) {
                return $restaurant->getTodayOrdersCount();
            }),
            'today_revenue' => $business->restaurants()->get()->sum(function($restaurant) {
                return $restaurant->getTodayRevenue();
            }),
        ];

        $recentRestaurants = $business->restaurants()
            ->with(['manager', 'orders'])
            ->latest()
            ->take(5)
            ->get();

        return view('business.dashboard', compact('business', 'stats', 'recentRestaurants'));
    }

    public function restaurants()
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        $restaurants = $business->restaurants()
            ->with(['manager', 'staff', 'categories', 'products', 'orders'])
            ->paginate(10);

        return view('business.restaurants', compact('business', 'restaurants'));
    }

    public function createRestaurant()
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        // Restoran yöneticisi olabilecek kullanıcıları getir
        $managers = User::where('role', 'restaurant_manager')->get();

        return view('business.create-restaurant', compact('business', 'managers'));
    }

    public function storeRestaurant(Request $request)
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'table_count' => 'required|integer|min:1|max:100',
            'restaurant_manager_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $restaurantData = $request->except('logo');
        $restaurantData['business_id'] = $business->id;

        // Logo yükleme
        if ($request->hasFile('logo')) {
            $restaurantData['logo'] = $request->file('logo')->store('restaurants', 'public');
        }

        $restaurant = Restaurant::create($restaurantData);

        // Restoran yöneticisini staff olarak ekle
        if ($request->restaurant_manager_id) {
            RestaurantStaff::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $request->restaurant_manager_id,
                'role' => 'restaurant_manager',
                'is_active' => true,
            ]);
        }

        return redirect()->route('business.restaurants')
            ->with('success', 'Restoran başarıyla oluşturuldu!');
    }

    public function showRestaurant(Restaurant $restaurant)
    {
        $user = Auth::user();
        
        if (!$user->canAccessRestaurant($restaurant)) {
            abort(403, 'Bu restorana erişim yetkiniz yok.');
        }

        $stats = [
            'total_categories' => $restaurant->categories()->count(),
            'total_products' => $restaurant->products()->count(),
            'total_staff' => $restaurant->staff()->where('is_active', true)->count(),
            'today_orders' => $restaurant->getTodayOrdersCount(),
            'today_revenue' => $restaurant->getTodayRevenue(),
        ];

        $recentOrders = $restaurant->orders()
            ->with('orderItems.product')
            ->latest()
            ->take(10)
            ->get();

        return view('business.restaurant-detail', compact('restaurant', 'stats', 'recentOrders'));
    }

    public function analytics()
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        $currentMonthStart = now()->startOfMonth();
        $monthlyRevenue = $business->restaurants()->get()->sum(function($restaurant) use ($currentMonthStart) {
            return $restaurant->orders()
                ->where('created_at', '>=', $currentMonthStart)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
        });

        $monthlyOrders = $business->restaurants()->get()->sum(function($restaurant) use ($currentMonthStart) {
            return $restaurant->orders()->where('created_at', '>=', $currentMonthStart)->count();
        });

        $stats = [
            'total_restaurants' => $business->restaurants()->count(),
            'active_restaurants' => $business->restaurants()->where('is_active', true)->count(),
            'total_products' => $business->total_products,
            'total_orders' => $business->total_orders,
            'total_staff' => RestaurantStaff::whereIn('restaurant_id', 
                $business->restaurants()->pluck('id')
            )->where('is_active', true)->count(),
            'monthly_revenue' => $monthlyRevenue,
            'monthly_orders' => $monthlyOrders,
        ];

        // Son 30 günün istatistikleri
        $dailyStats = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyOrders = $business->restaurants()->get()->sum(function($restaurant) use ($date) {
                return $restaurant->orders()->whereDate('created_at', $date)->count();
            });
            $dailyRevenue = $business->restaurants()->get()->sum(function($restaurant) use ($date) {
                return $restaurant->orders()
                    ->whereDate('created_at', $date)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total');
            });
            
            $dailyStats[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => $dailyOrders,
                'revenue' => $dailyRevenue,
            ];
        }

        return view('business.analytics', compact('business', 'stats', 'dailyStats'));
    }

    public function create()
    {
        return view('business.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $businessData = $request->except('logo');
        $businessData['owner_id'] = Auth::id();

        // Logo yükleme
        if ($request->hasFile('logo')) {
            $businessData['logo'] = $request->file('logo')->store('businesses', 'public');
        }

        $business = Business::create($businessData);

        return redirect()->route('business.dashboard')
            ->with('success', 'İşletmeniz başarıyla oluşturuldu!');
    }

    public function staffManagement()
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        $staff = RestaurantStaff::whereIn('restaurant_id', 
            $business->restaurants()->pluck('id')
        )
        ->with(['user', 'restaurant'])
        ->paginate(15);

        return view('business.staff', compact('business', 'staff'));
    }

    // Staff store
    public function storeStaff(Request $request)
    {
        $user = Auth::user();
        $business = $user->getActiveBusinesses()->first();
        if (!$business) {
            return redirect()->back()->with('error', 'Yetkiniz yok');
        }

        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'user_email' => 'required|email',
            'user_name'  => 'required|string|max:100',
            'role' => 'required|in:restaurant_manager,waiter,kitchen,cashier',
            'pin_code' => 'nullable|string|max:10',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        if ($restaurant->business_id !== $business->id) {
            return redirect()->back()->with('error', 'Restoran bu işletmeye ait değil');
        }

        // Kullanıcıyı bul veya oluştur
        $staffUser = User::where('email', $request->user_email)->first();
        if (!$staffUser) {
            $staffUser = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make('password'),
                'role' => $request->role,
            ]);
        }

        RestaurantStaff::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $staffUser->id,
            'role' => $request->role,
            'pin_code' => $request->pin_code,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Personel eklendi');
    }

    public function toggleStaffStatus(RestaurantStaff $staff)
    {
        $staff->is_active = !$staff->is_active;
        $staff->save();

        return response()->json(['success' => true]);
    }

    public function deleteStaff(RestaurantStaff $staff)
    {
        $staff->delete();
        return response()->json(['success' => true]);
    }

    // Restaurant edit forms
    public function editRestaurant(Restaurant $restaurant)
    {
        $user = Auth::user();
        if (!$user->canAccessRestaurant($restaurant)) {
            abort(403);
        }
        $managers = User::where('role', 'restaurant_manager')->get();
        return view('business.edit-restaurant', compact('restaurant','managers'));
    }

    public function updateRestaurant(Request $request, Restaurant $restaurant)
    {
        $user = Auth::user();
        if (!$user->canAccessRestaurant($restaurant)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'table_count' => 'required|integer|min:1|max:100',
            'restaurant_manager_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'working_hours_text' => 'nullable|string',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'translation_enabled' => 'nullable|boolean',
            'default_language' => 'nullable|string|max:10',
            'supported_languages' => 'nullable|array',
            'remove_logo' => 'nullable|boolean',
        ]);

        $data = $request->except(['logo', 'remove_logo']);
        
        // Çeviri ayarlarını işle
        $data['translation_enabled'] = $request->boolean('translation_enabled');
        $data['default_language'] = $request->default_language ?? 'tr';
        $data['supported_languages'] = $request->supported_languages ?? ['tr'];
        
        // Logo işlemleri
        if ($request->boolean('remove_logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
                $data['logo'] = null;
            }
        } elseif ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('restaurants', 'public');
        }
        


        $restaurant->update($data);

        return redirect()->route('business.restaurants.show', $restaurant->id)
            ->with('success', 'Restoran güncellendi');
    }

    /**
     * Show restaurant reviews
     */
    public function reviews(Restaurant $restaurant)
    {
        $user = Auth::user();
        if (!$user->canAccessRestaurant($restaurant)) {
            abort(403);
        }

        $reviews = $restaurant->reviews()
            ->with('restaurant')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('business.reviews', compact('restaurant', 'reviews'));
    }

    /**
     * Approve review
     */
    public function approveReview(Request $request, $reviewId)
    {
        $review = \App\Models\Review::findOrFail($reviewId);
        $user = Auth::user();
        
        if (!$user->canAccessRestaurant($review->restaurant)) {
            abort(403);
        }

        $review->update(['is_approved' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete review
     */
    public function deleteReview($reviewId)
    {
        $review = \App\Models\Review::findOrFail($reviewId);
        $user = Auth::user();
        
        if (!$user->canAccessRestaurant($review->restaurant)) {
            abort(403);
        }

        $review->delete();

        return response()->json(['success' => true]);
    }
}
