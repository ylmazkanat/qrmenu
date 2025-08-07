<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        if (!Auth::check() || !Auth::user()->business) {
            return $next($request);
        }

        $business = Auth::user()->business;

        // Eğer özellik belirtilmişse kontrol et
        if ($feature) {
            if (!$business->canAccessFeature($feature)) {
                return redirect()->route('business.packages.index')
                    ->with('error', 'Bu özelliği kullanmak için paketinizi yükseltmeniz gerekiyor.');
            }
        }

        // Aktif abonelik yoksa paket sayfasına yönlendir
        if (!$business->hasActiveSubscription()) {
            return redirect()->route('business.packages.index')
                ->with('error', 'Bu sayfayı kullanmak için aktif bir paket aboneliğiniz olması gerekiyor.');
        }

        return $next($request);
    }
}
