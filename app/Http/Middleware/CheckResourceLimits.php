<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckResourceLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resourceType = null): Response
    {
        if (!Auth::check() || !Auth::user()->business) {
            return $next($request);
        }

        $business = Auth::user()->business;

        // Aktif abonelik yoksa paket sayfasına yönlendir
        if (!$business->hasActiveSubscription()) {
            return redirect()->route('business.packages.index')
                ->with('error', 'Bu sayfayı kullanmak için aktif bir paket aboneliğiniz olması gerekiyor.');
        }

        // Eğer kaynak tipi belirtilmişse limit kontrolü yap
        if ($resourceType) {
            $featureKey = '';
            $currentCount = 0;
            $errorMessage = '';
            
            switch ($resourceType) {
                case 'restaurant':
                    $featureKey = 'max_restaurants';
                    $currentCount = $business->restaurant_count;
                    $errorMessage = 'Maksimum restoran sayısına ulaştınız. Daha fazla restoran eklemek için paketinizi yükseltmelisiniz.';
                    break;
                    
                case 'manager':
                    $featureKey = 'max_managers';
                    $currentCount = $business->manager_count;
                    $errorMessage = 'Maksimum müdür sayısına ulaştınız. Daha fazla müdür eklemek için paketinizi yükseltmelisiniz.';
                    break;
                    
                case 'staff':
                    $featureKey = 'max_staff';
                    $currentCount = $business->staff_count;
                    $errorMessage = 'Maksimum çalışan sayısına ulaştınız. Daha fazla çalışan eklemek için paketinizi yükseltmelisiniz.';
                    break;
                    
                case 'product':
                    $featureKey = 'max_products';
                    $currentCount = $business->product_count;
                    $errorMessage = 'Maksimum ürün sayısına ulaştınız. Daha fazla ürün eklemek için paketinizi yükseltmelisiniz.';
                    break;
                    
                case 'category':
                    $featureKey = 'max_categories';
                    $currentCount = $business->category_count;
                    $errorMessage = 'Maksimum kategori sayısına ulaştınız. Daha fazla kategori eklemek için paketinizi yükseltmelisiniz.';
                    break;
            }
            
            if ($featureKey) {
                $limit = $business->getFeatureLimit($featureKey);
                
                // Limit null ise sınırsız demektir, 0 ise özellik kapalı demektir
                if ($limit !== null && $limit !== 0 && $currentCount >= $limit) {
                    return redirect()->route('business.packages.index')
                        ->with('error', $errorMessage);
                }
            }
        }

        return $next($request);
    }
}