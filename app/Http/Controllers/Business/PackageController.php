<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\BusinessSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('packageFeatures')->where('is_active', true)->orderBy('sort_order')->get();
        $business = Auth::user()->business;
        $currentSubscription = $business->activeSubscription;
        if ($currentSubscription) {
            $currentSubscription->load('package.packageFeatures');
        }
        $subscriptionHistory = $business->subscriptions()->with('package')->orderBy('started_at', 'desc')->get();
        return view('business.packages.index', compact('packages', 'currentSubscription', 'business', 'subscriptionHistory'));
    }

    public function subscribe(Request $request, Package $package)
    {
        $business = Auth::user()->business;
        
        // Mevcut aboneliği kontrol et
        if ($business->hasActiveSubscription()) {
            return back()->with('error', 'Zaten aktif bir aboneliğiniz var.');
        }

        // Abonelik oluştur
        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'package_id' => $package->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'amount_paid' => $package->price,
            'payment_method' => 'manual', // Şimdilik manuel ödeme
            'transaction_id' => 'MANUAL_' . time(),
        ]);

        return redirect()->route('business.dashboard')->with('success', 'Paket aboneliği başarıyla oluşturuldu.');
    }

    public function upgrade(Request $request, Package $package)
    {
        $business = Auth::user()->business;
        $currentSubscription = $business->activeSubscription;
        
        if (!$currentSubscription) {
            return back()->with('error', 'Aktif aboneliğiniz bulunmuyor.');
        }

        // Mevcut aboneliği iptal et
        $currentSubscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Yeni abonelik oluştur
        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'package_id' => $package->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'amount_paid' => $package->price,
            'payment_method' => 'manual',
            'transaction_id' => 'UPGRADE_' . time(),
        ]);

        return redirect()->route('business.dashboard')->with('success', 'Paket başarıyla yükseltildi.');
    }

    public function cancel()
    {
        $business = Auth::user()->business;
        $subscription = $business->activeSubscription;
        
        if (!$subscription) {
            return back()->with('error', 'Aktif aboneliğiniz bulunmuyor.');
        }

        $subscription->update([
            'status' => 'pending_cancellation',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('business.packages.index')->with('warning', 'Paketiniz bitiş tarihinde otomatik olarak iptal edilecektir.');
    }
    
    /**
     * Abonelik ödemesi işlemi
     */
    public function showPaymentForm($subscription, $amount)
    {
        $subscription = BusinessSubscription::findOrFail($subscription);
        $business = Auth::user()->business;
        if ($subscription->business_id !== $business->id) {
            return back()->with('error', 'Bu aboneliğe erişim izniniz yok.');
        }
        return view('business.packages.payment', compact('subscription', 'amount'));
    }

    public function processPayment(Request $request, $subscription, $amount)
    {
        try {
            $subscription = BusinessSubscription::findOrFail($subscription);
            $business = Auth::user()->business;
            if ($subscription->business_id !== $business->id) {
                return back()->with('error', 'Bu aboneliğe erişim izniniz yok.');
            }
            // Ödeme işlemi burada gerçekleştirilecek
            // Gerçek bir ödeme entegrasyonu için burada ödeme API'si çağrılabilir
            DB::beginTransaction();
            $subscription->update([
                'is_paid' => true,
                'payment_date' => now(),
                'payment_method' => 'online',
                'transaction_id' => 'PAYMENT_' . time(),
            ]);
            DB::commit();
            return redirect()->route('business.packages.index')
                ->with('success', 'Ödeme başarıyla tamamlandı. Aboneliğiniz aktif edildi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }
}
