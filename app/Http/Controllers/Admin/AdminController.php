<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\Restaurant;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('is_active', true)->count(),
            'total_users' => User::count(),
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
        ];

        $recentBusinesses = Business::with('owner')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        $topBusinesses = Business::withCount(['restaurants'])
            ->orderByDesc('restaurants_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBusinesses', 'recentUsers', 'topBusinesses'));
    }

    public function businesses()
    {
        $businesses = Business::with(['owner', 'restaurants'])
            ->withCount(['restaurants'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_businesses' => Business::count(),
            'active_businesses' => Business::where('is_active', true)->count(),
            'free_plan' => Business::where('plan', 'free')->count(),
            'paid_plans' => Business::whereIn('plan', ['basic', 'premium', 'enterprise'])->count(),
        ];

        return view('admin.businesses', compact('businesses', 'stats'));
    }

    public function restaurants()
    {
        $restaurants = Restaurant::with(['business.owner', 'manager', 'staff'])
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
            'with_manager' => Restaurant::whereNotNull('restaurant_manager_id')->count(),
            'without_manager' => Restaurant::whereNull('restaurant_manager_id')->count(),
        ];

        return view('admin.restaurants', compact('restaurants', 'stats'));
    }

    public function users(Request $request)
    {
        // Dinamik filtreleme ve arama
        $query = User::query()
            ->with(['ownedBusinesses.restaurants', 'managedRestaurants', 'restaurantStaff.restaurant'])
            ->withCount(['ownedBusinesses', 'managedRestaurants', 'restaurantStaff']);

        // Arama kelimesi
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Rol filtresi
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'business_owners' => User::where('role', 'business_owner')->count(),
            'restaurant_managers' => User::where('role', 'restaurant_manager')->count(),
            'waiters' => User::where('role', 'waiter')->count(),
            'kitchen_staff' => User::where('role', 'kitchen')->count(),
            'cashiers' => User::where('role', 'cashier')->count(),
            'today_registered' => User::whereDate('created_at', today())->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function analytics()
    {
        $stats = [
            'total_businesses' => Business::count(),
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'monthly_orders' => Order::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue' => Order::whereMonth('created_at', now()->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
            // Paket abonelik istatistikleri
            'total_subscriptions' => \App\Models\BusinessSubscription::count(),
            'active_subscriptions' => \App\Models\BusinessSubscription::where('status', 'active')->count(),
            'monthly_subscription_revenue' => \App\Models\BusinessSubscription::whereMonth('payment_date', now()->month)
                ->where('is_paid', true)
                ->sum('amount_paid'),
            'total_subscription_revenue' => \App\Models\BusinessSubscription::where('is_paid', true)->sum('amount_paid'),
            // Son alınan paketler
            'recent_subscriptions' => \App\Models\BusinessSubscription::with(['business', 'package'])
                ->whereNotNull('payment_date')
                ->latest('payment_date')
                ->take(10)
                ->get(),
        ];

        // Son 30 günün istatistikleri
        $dailyStats = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyOrders = Order::whereDate('created_at', $date)->count();
            $dailyRevenue = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            $newBusinesses = Business::whereDate('created_at', $date)->count();
            $newUsers = User::whereDate('created_at', $date)->count();
            
            $dailyStats[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => $dailyOrders,
                'revenue' => $dailyRevenue,
                'businesses' => $newBusinesses,
                'users' => $newUsers,
            ];
        }

        // En aktif işletmeler
        $topBusinesses = Business::withCount(['restaurants as total_orders' => function($query) {
            $query->join('orders', 'restaurants.id', '=', 'orders.restaurant_id');
        }])
        ->orderByDesc('total_orders')
        ->take(10)
        ->get();

        // Paket kullanım istatistikleri
        $packageUsage = \App\Models\Package::withCount([
            'subscriptions as active_subscriptions' => function($query) {
                $query->where('status', 'active');
            },
            'subscriptions as total_subscriptions'
        ])->get();

        // Farklı zaman aralıkları için abonelik istatistikleri
        $subscriptionStats = [
            'monthly' => $this->getSubscriptionStats(30, 'daily'),
            'quarterly' => $this->getSubscriptionStats(90, 'weekly'),
            'halfyearly' => $this->getSubscriptionStats(180, 'weekly'),
            'yearly' => $this->getSubscriptionStats(365, 'monthly')
        ];

        // Plan dağılımı
        $planDistribution = [
            'free' => Business::where('plan', 'free')->count(),
            'basic' => Business::where('plan', 'basic')->count(),
            'premium' => Business::where('plan', 'premium')->count(),
            'enterprise' => Business::where('plan', 'enterprise')->count(),
        ];

        return view('admin.analytics', compact('stats', 'dailyStats', 'topBusinesses', 'planDistribution', 'packageUsage', 'subscriptionStats'));
    }

    private function getSubscriptionStats($days, $groupBy = 'daily')
    {
        $stats = [];
        $startDate = now()->subDays($days);
        
        if ($groupBy === 'daily') {
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $newSubscriptions = \App\Models\BusinessSubscription::whereDate('created_at', $date)->count();
                $subscriptionRevenue = \App\Models\BusinessSubscription::whereDate('payment_date', $date)
                    ->where('is_paid', true)
                    ->sum('amount_paid');
                
                $stats[] = [
                    'date' => $date->format('d.m'),
                    'subscriptions' => $newSubscriptions,
                    'revenue' => $subscriptionRevenue,
                ];
            }
        } elseif ($groupBy === 'weekly') {
            $weeks = ceil($days / 7);
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                
                $newSubscriptions = \App\Models\BusinessSubscription::whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $subscriptionRevenue = \App\Models\BusinessSubscription::whereBetween('payment_date', [$weekStart, $weekEnd])
                    ->where('is_paid', true)
                    ->sum('amount_paid');
                
                $stats[] = [
                    'date' => $weekStart->format('d.m') . '-' . $weekEnd->format('d.m'),
                    'subscriptions' => $newSubscriptions,
                    'revenue' => $subscriptionRevenue,
                ];
            }
        } elseif ($groupBy === 'monthly') {
            $months = ceil($days / 30);
            for ($i = $months - 1; $i >= 0; $i--) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                
                $newSubscriptions = \App\Models\BusinessSubscription::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $subscriptionRevenue = \App\Models\BusinessSubscription::whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->where('is_paid', true)
                    ->sum('amount_paid');
                
                $stats[] = [
                    'date' => $monthStart->format('M Y'),
                    'subscriptions' => $newSubscriptions,
                    'revenue' => $subscriptionRevenue,
                ];
            }
        }
        
        return $stats;
    }

    /**
     * Admin olarak bir kullanıcının hesabına giriş yap (impersonate)
     */
    public function impersonate(User $user)
    {
        // Halihazırda impersonate modunda değilsek admin ID'yi sakla
        if (!session()->has('impersonate')) {
            session()->put('impersonate', auth()->id());
        }

        auth()->login($user);

        return redirect()->route('home');
    }

    public function showBusiness(Business $business)
    {
        $business->load(['owner', 'restaurants.manager', 'restaurants.staff.user']);
        
        $stats = [
            'total_restaurants' => $business->restaurants()->count(),
            'active_restaurants' => $business->restaurants()->where('is_active', true)->count(),
            'total_products' => $business->total_products,
            'total_orders' => $business->total_orders,
            'total_staff' => $business->restaurants()->get()->sum(function($restaurant) {
                return $restaurant->staff()->where('is_active', true)->count();
            }),
            'monthly_revenue' => $business->restaurants()->get()->sum(function($restaurant) {
                return $restaurant->orders()
                    ->whereMonth('created_at', now()->month)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total');
            }),
        ];

        $recentOrders = Order::whereIn('restaurant_id', $business->restaurants()->pluck('id'))
            ->with(['restaurant', 'orderItems.product'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.business-detail', compact('business', 'stats', 'recentOrders'));
    }

    public function toggleBusinessStatus(Business $business)
    {
        $business->update(['is_active' => !$business->is_active]);
        
        $status = $business->is_active ? 'aktif' : 'pasif';
        return response()->json([
            'success' => true,
            'message' => "İşletme {$status} duruma getirildi.",
            'status' => $business->is_active
        ]);
    }

    public function toggleRestaurantStatus(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => !$restaurant->is_active]);
        
        $status = $restaurant->is_active ? 'aktif' : 'pasif';
        return response()->json([
            'success' => true,
            'message' => "Restoran {$status} duruma getirildi.",
            'status' => $restaurant->is_active
        ]);
    }
}
