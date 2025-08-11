@extends('layouts.business')

@section('content')
<style>
    .packages-container {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }
    
    .packages-row {
        margin-left: 0;
        margin-right: 0;
    }
    
    .package-col {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    .current-subscription-card {
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 2rem;
    }
    
    .subscription-history-table {
        margin-left: 0;
        margin-right: 0;
    }
</style>
<div class="container-fluid px-4 py-5">
    <h1 class="mb-4">Kullanılabilir Paketler</h1>
    
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($currentSubscription)
        <div class="card mb-5 shadow current-subscription-card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Mevcut Paketiniz: {{ $currentSubscription->package->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row packages-row">
                    <div class="col-md-3">
                        <p><strong>Açıklama:</strong> {{ $currentSubscription->package->description ?? 'Standart özellikler' }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Fiyat:</strong> {{ number_format($currentSubscription->package->price, 2) }} ₺ / {{ ucfirst($currentSubscription->package->billing_cycle) }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Bitiş Tarihi:</strong> {{ $currentSubscription->expires_at->format('d.m.Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        @php
                            // Son ödeme tarihi, bitiş tarihinden 7 gün önce olsun
                            $paymentDueDate = $currentSubscription->expires_at->copy()->subDays(7)->format('d.m.Y');
                            $isPaid = isset($currentSubscription->is_paid) ? $currentSubscription->is_paid : false; // Ödeme durumu kontrolü
                            $amount = $currentSubscription->package->price;
                        @endphp
                        <p><strong>Son Ödeme Tarihi:</strong> {{ $paymentDueDate }}</p>
                        @if(!$isPaid && $currentSubscription->status == 'active')
                            <form action="{{ route('business.packages.payment', ['subscription' => $currentSubscription->id, 'amount' => $amount]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Öde</button>
                            </form>
                        @elseif($isPaid)
                            <span class="badge bg-success"><i class="fas fa-check"></i> Ödendi</span>
                        @endif
                    </div>
                </div>
                <h5 class="mt-4">Özellik Kullanım Durumu</h5>
                <div class="row packages-row">
                    @foreach($currentSubscription->package->packageFeatures as $feature)
                        @php
                            // Sayısal limitler (max_ ile başlayanlar)
                            $isNumericLimit = in_array($feature->feature_key, ['max_restaurants', 'max_managers', 'max_staff', 'max_products', 'max_categories']);
                            
                            // Sayısal limitler için mevcut koşulları koru
                            if($isNumericLimit) {
                                $shouldShow = $feature->is_enabled;
                            } else {
                                // Diğer özellikler için tümünü göster (0 ve 1 dahil)
                                $shouldShow = true;
                            }
                        @endphp
                        
                        @if($shouldShow)
                            <div class="col-md-4 mb-2">
                                @if($feature->is_coming_soon)
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $feature->feature_name }} (Yakında)
                                    </span>
                                @else
                                    @php
                                        $usage = 0;
                                        $displayText = '';
                                        $featureName = '';
                                        
                                        switch($feature->feature_key) {
                                            case 'max_restaurants': 
                                                $featureName = 'Maksimum Restoran Sayısı';
                                                $usage = $business->restaurant_count; 
                                                break;
                                            case 'max_managers': 
                                                $featureName = 'Maksimum Müdür Hesabı';
                                                $usage = $business->manager_count; 
                                                break;
                                            case 'max_staff': 
                                                $featureName = 'Maksimum Çalışan Sayısı';
                                                $usage = $business->staff_count; 
                                                break;
                                            case 'max_products': 
                                                $featureName = 'Restoran Maksimum Ürün Limiti';
                                                break;
                                            case 'max_categories': 
                                                $featureName = 'Restoran Maksimum Kategori Limiti';
                                                break;
                                            default:
                                                $featureName = $feature->feature_name;
                                        }
                                        
                                        if($isNumericLimit) {
                                            // Sayısal limitler için yeşil badge ve mevcut gösterim
                                            $badgeClass = 'bg-success';
                                            $iconClass = 'fas fa-check';
                                            if($feature->limit_value && $feature->limit_value > 0) {
                                                if(in_array($feature->feature_key, ['max_restaurants', 'max_managers', 'max_staff'])) {
                                                    $displayText = $featureName . ': ' . $usage . ' / ' . $feature->limit_value;
                                                } else {
                                                    $displayText = $featureName . ': ' . $feature->limit_value;
                                                }
                                            } elseif($feature->limit_value == 0 || $feature->isUnlimited()) {
                                                $displayText = $featureName . ': Sınırsız';
                                            } else {
                                                $displayText = $featureName . ': Etkin';
                                            }
                                        } else {
                                            // Diğer özellikler için renk ve ikon belirleme
                                            if($feature->limit_value == 1) {
                                                $badgeClass = 'bg-success';
                                                $iconClass = 'fas fa-check';
                                            } else {
                                                $badgeClass = 'bg-danger';
                                                $iconClass = 'fas fa-times';
                                            }
                                            $displayText = $featureName;
                                        }
                                    @endphp
                                    
                                    <span class="badge {{ $badgeClass }}">
                                        <i class="{{ $iconClass }} me-1"></i>
                                        {{ $displayText }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        @foreach($packages as $package)
            <div class="col-md-6 mb-4 package-col">
                <div class="card h-100 shadow @if($currentSubscription && $currentSubscription->package_id == $package->id) border-success @else border-success @endif">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0">{{ $package->name }} @if($package->is_popular)<span class="badge bg-warning text-dark">Popüler</span>@endif</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h3 class="fw-bold">{{ number_format($package->price, 2) }} ₺ / {{ ucfirst($package->billing_cycle) }}</h3>
                            <p class="text-muted">{{ $package->description ?? 'Standart özellikler' }}</p>
                        </div>
                        <div class="row">
                            @foreach($package->packageFeatures as $feature)
                                @php
                                    // Sayısal limitler (max_ ile başlayanlar)
                                    $isNumericLimit = in_array($feature->feature_key, ['max_restaurants', 'max_managers', 'max_staff', 'max_products', 'max_categories']);
                                    
                                    // Sayısal limitler için mevcut koşulları koru
                                    if($isNumericLimit) {
                                        $shouldShow = $feature->is_enabled || $feature->limit_value == 0 || $feature->isUnlimited();
                                    } else {
                                        // Diğer özellikler için tümünü göster (0 ve 1 dahil)
                                        $shouldShow = true;
                                    }
                                @endphp
                                
                                @if($shouldShow)
                                    <div class="col-6 mb-2">
                                        @if($feature->is_coming_soon)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $feature->feature_name }}
                                            </span>
                                        @else
                                            @php
                                                switch($feature->feature_key) {
                                                    case 'max_restaurants': 
                                                        $displayText = 'Maksimum Restoran Sayısı';
                                                        break;
                                                    case 'max_managers': 
                                                        $displayText = 'Maksimum Müdür Hesabı';
                                                        break;
                                                    case 'max_staff': 
                                                        $displayText = 'Maksimum Çalışan Sayısı';
                                                        break;
                                                    case 'max_products': 
                                                        $displayText = 'Restoran Maksimum Ürün Limiti';
                                                        break;
                                                    case 'max_categories': 
                                                        $displayText = 'Restoran Maksimum Kategori Limiti';
                                                        break;
                                                    default:
                                                        $displayText = $feature->feature_name;
                                                }
                                                
                                                if($isNumericLimit) {
                                                    // Sayısal limitler için yeşil badge ve mevcut gösterim
                                                    $badgeClass = 'bg-success';
                                                    $iconClass = 'fas fa-check';
                                                } else {
                                                    // Diğer özellikler için renk ve ikon belirleme
                                                    if($feature->limit_value == 1) {
                                                        $badgeClass = 'bg-success';
                                                        $iconClass = 'fas fa-check';
                                                    } else {
                                                        $badgeClass = 'bg-danger';
                                                        $iconClass = 'fas fa-times';
                                                    }
                                                }
                                            @endphp
                                            
                                            <span class="badge {{ $badgeClass }}">
                                                <i class="{{ $iconClass }} me-1"></i>
                                                
                                                @if($isNumericLimit)
                                                    {{-- Sayısal limitler için mevcut gösterim --}}
                                                    {{ $displayText }} :
                                                    @if($feature->limit_value && $feature->limit_value > 0)
                                                        {{ $feature->limit_value }}
                                                    @elseif($feature->limit_value == 0 || $feature->isUnlimited())
                                                        Sınırsız
                                                    @else
                                                        Etkin
                                                    @endif
                                                @else
                                                    {{-- Diğer özellikler için sadece başlık --}}
                                                    {{ $displayText }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        @if($currentSubscription && $currentSubscription->package_id == $package->id)
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-success" disabled>Paketiniz</button>
                                <form action="{{ route('business.packages.cancel') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">İptal</button>
                                </form>
                            </div>
                            <small class="d-block mt-2">Bitiş: {{ $currentSubscription->expires_at->format('d.m.Y') }}</small>
                        @else
                            <form action="{{ route('business.packages.' . ($currentSubscription ? 'upgrade' : 'subscribe'), $package) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">{{ $currentSubscription ? 'Pakete Geç' : 'Satın Al' }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($subscriptionHistory->count() > 0)
        <h2 class="mt-5">Eski Paket Hareketleri</h2>
        <table class="table table-bordered subscription-history-table">
            <thead>
                <tr>
                    <th>Paket</th>
                    <th>Başlangıç Tarihi</th>
                    <th>Bitiş/İptal Tarihi</th>
                    <th>Son Ödeme Tarihi</th>
                    <th>Durum</th>
                    <th>Tutar</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptionHistory as $sub)
                    <tr>
                        <td>{{ $sub->package->name }}</td>
                        <td>{{ $sub->started_at->format('d.m.Y') }}</td>
                        <td>
                            @if($sub->cancelled_at)
                                {{ $sub->cancelled_at->format('d.m.Y') }} (İptal)
                            @elseif($sub->expires_at)
                                {{ $sub->expires_at->format('d.m.Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                // Son ödeme tarihi, bitiş tarihinden 7 gün önce olsun
                                $paymentDueDate = $sub->expires_at ? $sub->expires_at->copy()->subDays(7)->format('d.m.Y') : '-';
                            @endphp
                            {{ $paymentDueDate }}
                        </td>
                        <td>
@php
$statusMap = [
'active' => 'Aktif: Abonelik halen geçerli ve kullanımda.',
'pending_cancellation' => 'İptal Bekleniyor: Abonelik iptal talebi alındı, bitiş tarihinde otomatik iptal edilecek.',
'cancelled' => 'İptal Edildi: Abonelik iptal edildi.',
'expired' => 'Süresi Doldu: Abonelik süresi sona erdi.',
'inactive' => 'Pasif: Abonelik etkin değil.'
];
echo $statusMap[$sub->status] ?? ucfirst($sub->status);
@endphp
</td>
                        <td>
                            @php
                                $hoursTotal = $sub->started_at->diffInHours($sub->expires_at ?? now());
                                $daysTotal = max(1, $hoursTotal / 24.0); // Avoid division by zero
                                $endDate = $sub->cancelled_at ?? ($sub->expires_at ?? now());
                                $hoursUsed = $sub->started_at->diffInHours($endDate);
                                $daysUsedFloat = $hoursUsed / 24.0;
                                // 0.5'ten büyükse 1 güne, küçükse 0 güne yuvarla
                                $daysUsed = ($daysUsedFloat > 0.5) ? ceil($daysUsedFloat) : floor($daysUsedFloat);
                                $daysUsed = (int)$daysUsed; // Tam sayıya dönüştür
                                $prorated = round(($sub->amount_paid / $daysTotal) * $daysUsed, 2);
                                $isPaid = isset($sub->is_paid) ? $sub->is_paid : false; // Ödeme durumu kontrolü
                            @endphp
                            {{ $prorated }} ₺ ({{ $daysUsed }} gün kullanım)
                        </td>
                        <td>
                            @if($sub->status == 'active')
                                @if($isPaid)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Ödendi</span>
                                @else
                                    <a href="{{ route('business.packages.payment', ['subscription' => $sub->id, 'amount' => $prorated]) }}" class="btn btn-sm btn-primary">Öde</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mt-5">Henüz paket hareketi bulunmuyor.</p>
    @endif
</div>
@endsection