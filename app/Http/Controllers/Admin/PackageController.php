<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\BusinessSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['packageFeatures', 'subscriptions'])
            ->withCount([
                'subscriptions as active_subscriptions_count' => function ($query) {
                    $query->where('is_active', true);
                },
                'packageFeatures as enabled_features_count' => function ($query) {
                    $query->where('is_enabled', true);
                },
                'packageFeatures as total_features_count'
            ])
            ->orderBy('sort_order')
            ->get();
        
        // Ek veriler - Her işletmenin sadece son aktif aboneliğini göster
        $activeSubscriptions = BusinessSubscription::with(['business', 'package'])
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('business_subscriptions')
                    ->where('status', 'active')
                    ->groupBy('business_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Tüm abonelik logları (silme işlemi için)
        $subscriptions = BusinessSubscription::with(['business', 'package'])->orderBy('created_at', 'desc')->get();
        $businesses = \App\Models\Business::orderBy('name')->get();
        
        // Günlük kazançlar - iptal edilenler ve ödeme bekleyenler hariç
        $dailyEarnings = BusinessSubscription::selectRaw('DATE(payment_date) as date, SUM(amount_paid) as total')
            ->whereNotNull('payment_date')
            ->where('is_paid', true)
            ->whereNotIn('status', ['cancelled', 'pending_payment'])
            ->where('payment_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        $monthlyEarnings = BusinessSubscription::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->whereNotNull('payment_date')
            ->where('is_paid', true)
            ->whereNotIn('status', ['cancelled', 'pending_payment'])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        $annualEarnings = BusinessSubscription::selectRaw('YEAR(payment_date) as year, SUM(amount_paid) as total')
            ->whereNotNull('payment_date')
            ->where('is_paid', true)
            ->whereNotIn('status', ['cancelled', 'pending_payment'])
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();
        $totalEarnings = BusinessSubscription::whereNotNull('payment_date')
            ->where('is_paid', true)
            ->whereNotIn('status', ['cancelled', 'pending_payment'])
            ->sum('amount_paid');
        
        // Grafik verileri
        $chartData = [
            'daily' => $dailyEarnings->map(function($item) {
                return ['label' => $item->date, 'value' => $item->total];
            }),
            'monthly' => $monthlyEarnings->map(function($item) {
                return ['label' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT), 'value' => $item->total];
            }),
            'annual' => $annualEarnings->map(function($item) {
                return ['label' => $item->year, 'value' => $item->total];
            }),
        ];
        
        
        
        // Dashboard istatistikleri için ek hesaplamalar
        $activeSubscriptionsCount = BusinessSubscription::where('status', '!=', 'cancelled')->count();
        $pendingPaymentsCount = BusinessSubscription::where('status', '!=', 'cancelled')
            ->where('is_paid', false)
            ->count();
        
        return view('admin.packages.index', compact('packages', 'subscriptions', 'activeSubscriptions', 'businesses', 'monthlyEarnings', 'annualEarnings', 'totalEarnings', 'chartData', 'activeSubscriptionsCount', 'pendingPaymentsCount'));
    }

    public function create()
    {
        $predefinedFeatures = [
            'max_restaurants' => [
                'name' => 'Maksimum Restoran Sayısı',
                'description' => 'İşletmenin oluşturabileceği maksimum restoran sayısı',
                'type' => 'number'
            ],
            'max_managers' => [
                'name' => 'Maksimum Müdür Hesabı',
                'description' => 'İşletmenin oluşturabileceği maksimum müdür hesabı sayısı',
                'type' => 'number'
            ],
            'max_staff' => [
                'name' => 'Maksimum Çalışan Sayısı',
                'description' => 'İşletmenin oluşturabileceği maksimum çalışan sayısı',
                'type' => 'number'
            ],
            'max_products' => [
                'name' => 'Restoran Maksimum Ürün Limiti',
                'description' => 'Her restoran için maksimum ürün sayısı',
                'type' => 'number'
            ],
            'max_categories' => [
                'name' => 'Restoran Maksimum Kategori Limiti',
                'description' => 'Her restoran için maksimum kategori sayısı',
                'type' => 'number'
            ],
            'custom_domain' => [
                'name' => 'Özel Domain Desteği',
                'description' => 'Kendi domain adresini kullanma hakkı',
                'type' => 'boolean'
            ],
            'analytics' => [
                'name' => 'Analitik Raporları',
                'description' => 'Detaylı satış ve müşteri analiz raporları',
                'type' => 'boolean'
            ],
            'multi_language' => [
                'name' => 'Çoklu Dil Desteği',
                'description' => 'Menüleri farklı dillerde yayınlama',
                'type' => 'boolean'
            ],
            'priority_support' => [
                'name' => 'Öncelikli Destek',
                'description' => '7/24 öncelikli teknik destek',
                'type' => 'boolean'
            ],
            'advanced_menu_editor' => [
                'name' => 'Gelişmiş Menü Editörü',
                'description' => 'Gelişmiş menü düzenleme araçları',
                'type' => 'boolean'
            ],
            'order_management' => [
                'name' => 'Sipariş Yönetimi',
                'description' => 'Gelişmiş sipariş takip ve yönetim sistemi',
                'type' => 'boolean'
            ],
            'customer_reviews' => [
                'name' => 'Müşteri Değerlendirmeleri',
                'description' => 'Müşteri yorumları ve puanlama sistemi',
                'type' => 'boolean'
            ],
            'loyalty_program' => [
                'name' => 'Sadakat Programı',
                'description' => 'Müşteri sadakat programı ve indirimler',
                'type' => 'boolean'
            ],
            'marketing_tools' => [
                'name' => 'Pazarlama Araçları',
                'description' => 'E-posta pazarlama ve kampanya araçları',
                'type' => 'boolean'
            ],
            'api_access' => [
                'name' => 'API Erişimi',
                'description' => 'REST API erişimi ve entegrasyon',
                'type' => 'boolean'
            ]
        ];

        return view('admin.packages.create', compact('predefinedFeatures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'is_active' => 'nullable',
            'is_popular' => 'nullable',
            'sort_order' => 'integer|min:0',
        ]);

        try {


            $package = Package::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'is_active' => $request->has('is_active') || $request->input('is_active') === 'on',
                'is_popular' => $request->has('is_popular') || $request->input('is_popular') === 'on',
                'sort_order' => (int) $request->sort_order,
            ]);



            // Özellikleri ekle
            $predefinedFeatures = [
                'max_restaurants' => 'Maksimum Restoran Sayısı',
                'max_managers' => 'Maksimum Müdür Hesabı',
                'max_staff' => 'Maksimum Çalışan Sayısı',
                'max_products' => 'Restoran Maksimum Ürün Limiti',
                'max_categories' => 'Restoran Maksimum Kategori Limiti',
                'custom_domain' => 'Özel Domain Desteği',
                'analytics' => 'Analitik Raporları',
                'multi_language' => 'Çoklu Dil Desteği',
                'priority_support' => 'Öncelikli Destek',
                'advanced_menu_editor' => 'Gelişmiş Menü Editörü',
                'order_management' => 'Sipariş Yönetimi',
                'customer_reviews' => 'Müşteri Değerlendirmeleri',
                'loyalty_program' => 'Sadakat Programı',
                'marketing_tools' => 'Pazarlama Araçları',
                'api_access' => 'API Erişimi'
            ];

            foreach ($predefinedFeatures as $key => $name) {
                $limitValue = null;
                $isEnabled = false;

                if (in_array($key, ['max_restaurants', 'max_managers', 'max_staff', 'max_products', 'max_categories'])) {
                    // Sayısal değerler
                    $limitValue = (int) $request->input("feature_{$key}", 0);
                    $isEnabled = $limitValue > 0;
                } else {
                    // Boolean değerler
                    $isEnabled = $request->has("feature_{$key}");
                    $limitValue = $isEnabled ? 1 : 0;
                }



                // Her özelliği ekle (aktif olmasa bile)
                PackageFeature::create([
                    'package_id' => $package->id,
                    'feature_key' => $key,
                    'feature_name' => $name,
                    'description' => $name . ' özelliği',
                    'limit_value' => $limitValue,
                    'is_enabled' => $isEnabled,
                    'sort_order' => array_search($key, array_keys($predefinedFeatures)) + 1,
                ]);
            }


            return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Paket oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    public function edit(Package $package)
    {
        // Manuel olarak features'ları yükle
        $features = \App\Models\PackageFeature::where('package_id', $package->id)->get()->keyBy('feature_key');
        

        
        $predefinedFeatures = [
            'max_restaurants' => [
                'name' => 'Maksimum Restoran Sayısı',
                'description' => 'İşletmenin oluşturabileceği maksimum restoran sayısı',
                'type' => 'number'
            ],
            'max_managers' => [
                'name' => 'Maksimum Müdür Hesabı',
                'description' => 'İşletmenin oluşturabileceği maksimum müdür hesabı sayısı',
                'type' => 'number'
            ],
            'max_staff' => [
                'name' => 'Maksimum Çalışan Sayısı',
                'description' => 'İşletmenin oluşturabileceği maksimum çalışan sayısı',
                'type' => 'number'
            ],
            'max_products' => [
                'name' => 'Restoran Maksimum Ürün Limiti',
                'description' => 'Her restoran için maksimum ürün sayısı',
                'type' => 'number'
            ],
            'max_categories' => [
                'name' => 'Restoran Maksimum Kategori Limiti',
                'description' => 'Her restoran için maksimum kategori sayısı',
                'type' => 'number'
            ],
            'custom_domain' => [
                'name' => 'Özel Domain Desteği',
                'description' => 'Kendi domain adresini kullanma hakkı',
                'type' => 'boolean'
            ],
            'analytics' => [
                'name' => 'Analitik Raporları',
                'description' => 'Detaylı satış ve müşteri analiz raporları',
                'type' => 'boolean'
            ],
            'multi_language' => [
                'name' => 'Çoklu Dil Desteği',
                'description' => 'Menüleri farklı dillerde yayınlama',
                'type' => 'boolean'
            ],
            'priority_support' => [
                'name' => 'Öncelikli Destek',
                'description' => '7/24 öncelikli teknik destek',
                'type' => 'boolean'
            ],
            'advanced_menu_editor' => [
                'name' => 'Gelişmiş Menü Editörü',
                'description' => 'Gelişmiş menü düzenleme araçları',
                'type' => 'boolean'
            ],
            'order_management' => [
                'name' => 'Sipariş Yönetimi',
                'description' => 'Gelişmiş sipariş takip ve yönetim sistemi',
                'type' => 'boolean'
            ],
            'customer_reviews' => [
                'name' => 'Müşteri Değerlendirmeleri',
                'description' => 'Müşteri yorumları ve puanlama sistemi',
                'type' => 'boolean'
            ],
            'loyalty_program' => [
                'name' => 'Sadakat Programı',
                'description' => 'Müşteri sadakat programı ve indirimler',
                'type' => 'boolean'
            ],
            'marketing_tools' => [
                'name' => 'Pazarlama Araçları',
                'description' => 'E-posta pazarlama ve kampanya araçları',
                'type' => 'boolean'
            ],
            'api_access' => [
                'name' => 'API Erişimi',
                'description' => 'REST API erişimi ve entegrasyon',
                'type' => 'boolean'
            ]
        ];

        return view('admin.packages.edit', compact('package', 'predefinedFeatures', 'features'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'is_active' => 'nullable',
            'is_popular' => 'nullable',
            'sort_order' => 'integer|min:0',
        ]);

        try {


            $package->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'is_active' => $request->has('is_active') || $request->input('is_active') === 'on',
                'is_popular' => $request->has('is_popular') || $request->input('is_popular') === 'on',
                'sort_order' => (int) $request->sort_order,
            ]);



            // Mevcut özellikleri güncelle
            $predefinedFeatures = [
                'max_restaurants' => 'Maksimum Restoran Sayısı',
                'max_managers' => 'Maksimum Müdür Hesabı',
                'max_staff' => 'Maksimum Çalışan Sayısı',
                'max_products' => 'Maksimum Ürün Sayısı',
                'max_categories' => 'Maksimum Kategori Sayısı',
                'custom_domain' => 'Özel Domain Desteği',
                'analytics' => 'Analitik Raporları',
                'multi_language' => 'Çoklu Dil Desteği',
                'priority_support' => 'Öncelikli Destek',
                'advanced_menu_editor' => 'Gelişmiş Menü Editörü',
                'order_management' => 'Sipariş Yönetimi',
                'customer_reviews' => 'Müşteri Değerlendirmeleri',
                'loyalty_program' => 'Sadakat Programı',
                'marketing_tools' => 'Pazarlama Araçları',
                'api_access' => 'API Erişimi'
            ];

            foreach ($predefinedFeatures as $key => $name) {
                $feature = $package->packageFeatures()->where('feature_key', $key)->first();
                
                if (!$feature) {
                    // Özellik yoksa oluştur
                    $feature = new PackageFeature();
                    $feature->package_id = $package->id;
                    $feature->feature_key = $key;
                    $feature->feature_name = $name;
                    $feature->description = $name . ' özelliği';
                    $feature->sort_order = array_search($key, array_keys($predefinedFeatures)) + 1;
                }

                if (in_array($key, ['max_restaurants', 'max_managers', 'max_staff', 'max_products', 'max_categories'])) {
                    // Sayısal değerler
                    $limitValue = (int) $request->input("feature_{$key}", 0);
                    $feature->limit_value = $limitValue;
                    $feature->is_enabled = $limitValue > 0;
                } else {
                    // Boolean değerler
                    $feature->is_enabled = $request->has("feature_{$key}");
                    $feature->limit_value = $feature->is_enabled ? 1 : 0;
                }



                $feature->save();
            }

            return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla güncellendi.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Paket güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy(Package $package)
    {
        if ($package->subscriptions()->exists()) {
            return back()->with('error', 'Bu pakete abone olan işletmeler var. Önce abonelikleri iptal edin.');
        }

        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla silindi.');
    }
    
    public function addSubscription(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'package_id' => 'required|exists:packages,id',
            'expires_at' => 'required|date|after:today',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,expired'
        ]);
        
        DB::transaction(function () use ($request) {
            // Bu işletmenin TÜM aktif aboneliklerini iptal et
            BusinessSubscription::where('business_id', $request->business_id)
                ->whereIn('status', ['active', 'pending_cancellation'])
                ->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now()
                ]);
            
            // Yeni abonelik oluştur
            BusinessSubscription::create([
                'business_id' => $request->business_id,
                'package_id' => $request->package_id,
                'status' => $request->status,
                'started_at' => now(),
                'expires_at' => $request->expires_at,
                'amount_paid' => $request->amount_paid,
                'payment_date' => $request->payment_date,
                'payment_method' => 'admin_manual',
                'transaction_id' => 'ADMIN_' . time(),
                'is_paid' => $request->payment_date ? true : false
            ]);
        });
        
        return back()->with('success', 'Abonelik başarıyla oluşturuldu. Eski abonelikler iptal edildi.');
    }
    
    public function updateSubscription(Request $request, $id)
    {
        $request->validate([
            'expires_at' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,expired,pending_cancellation'
        ]);
        
        $subscription = BusinessSubscription::findOrFail($id);
        
        $subscription->update([
            'expires_at' => $request->expires_at,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'status' => $request->status,
            'is_paid' => $request->payment_date ? true : false
        ]);
        
        return back()->with('success', 'Abonelik başarıyla güncellendi.');
    }
    
    public function deleteSubscription($id)
    {
        $subscription = BusinessSubscription::findOrFail($id);
        $subscription->delete();
        
        return back()->with('success', 'Abonelik logu başarıyla silindi.');
    }
}
