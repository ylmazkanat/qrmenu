@extends('layouts.admin')

@section('title', 'İşletme Detayı - QR Menu')
@section('page-title', $business->name . ' - Detaylar')

@section('content')
    <!-- İşletme Bilgi Başlığı -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            @if($business->logo)
                                <img src="{{ Storage::url($business->logo) }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-success rounded text-white d-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="bi bi-building fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-2">{{ $business->name }}</h3>
                            @if($business->description)
                                <p class="text-muted mb-2">{{ $business->description }}</p>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <i class="bi bi-person me-2"></i>
                                        <strong>Sahibi:</strong> {{ $business->owner->name }}
                                    </div>
                                    <div class="mb-1">
                                        <i class="bi bi-envelope me-2"></i>
                                        {{ $business->owner->email }}
                                    </div>
                                    @if($business->phone)
                                        <div class="mb-1">
                                            <i class="bi bi-telephone me-2"></i>
                                            {{ $business->phone }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($business->tax_number)
                                        <div class="mb-1">
                                            <i class="bi bi-file-text me-2"></i>
                                            <strong>Vergi No:</strong> {{ $business->tax_number }}
                                        </div>
                                    @endif
                                    <div class="mb-1">
                                        <i class="bi bi-award me-2"></i>
                                        <strong>Plan:</strong> 
                                        <span class="badge bg-{{ $business->plan === 'free' ? 'secondary' : 'success' }}">
                                            {{ strtoupper($business->plan) }}
                                        </span>
                                    </div>
                                    <div class="mb-1">
                                        @if($business->is_active)
                                            <span class="badge badge-modern bg-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-modern bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Pasif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="d-flex flex-column gap-2">
                                <form method="POST" action="{{ route('admin.businesses.toggle-status', $business) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $business->is_active ? 'warning' : 'success' }} btn-sm w-100">
                                        <i class="bi bi-{{ $business->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $business->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                    </button>
                                </form>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-gear"></i>
                                    Düzenle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stats-value">{{ $stats['total_restaurants'] }}</div>
                <div class="stats-label">Restoran</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    {{ $stats['active_restaurants'] }} aktif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-menu-app"></i>
                </div>
                <div class="stats-value">{{ $stats['total_products'] }}</div>
                <div class="stats-label">Ürün</div>
                <div class="stats-change positive">
                    <i class="bi bi-plus"></i>
                    Tüm restoranlar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-value">{{ $stats['total_orders'] }}</div>
                <div class="stats-label">Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    Tüm zamanlar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-value">{{ $stats['total_staff'] }}</div>
                <div class="stats-label">Personel</div>
                <div class="stats-change positive">
                    <i class="bi bi-person-check"></i>
                    Aktif çalışan
                </div>
            </div>
        </div>
    </div>

    <!-- Restoranlar ve Aylık Gelir -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-shop me-2"></i>
                        Restoranlar
                        <span class="badge bg-primary ms-2">{{ $business->restaurants()->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($business->restaurants()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Restoran</th>
                                        <th>Müdür</th>
                                        <th>Personel</th>
                                        <th>Durum</th>
                                        <th>Ürün</th>
                                        <th>Sipariş</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($business->restaurants as $restaurant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($restaurant->logo)
                                                            <img src="{{ Storage::url($restaurant->logo) }}" 
                                                                 class="rounded" width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="bi bi-shop"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $restaurant->name }}</div>
                                                        <small class="text-muted">{{ $restaurant->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($restaurant->manager)
                                                    <div>{{ $restaurant->manager->name }}</div>
                                                    <small class="text-muted">{{ $restaurant->manager->email }}</small>
                                                @else
                                                    <span class="text-muted">Atanmamış</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $restaurant->staff()->where('is_active', true)->count() }}</span>
                                            </td>
                                            <td>
                                                @if($restaurant->is_active)
                                                    <span class="badge badge-modern bg-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-modern bg-danger">Pasif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">{{ $restaurant->products()->count() }}</span>
                                            </td>
                                            <td>
                                                <span class="text-primary fw-bold">{{ $restaurant->orders()->count() }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 3rem;">
                                <i class="bi bi-shop"></i>
                            </div>
                            <h5 class="text-muted">Henüz restoran oluşturulmamış</h5>
                            <p class="text-muted">İlk restoranın oluşturulmasını bekliyoruz.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Aylık Performans
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="text-success fw-bold fs-3">₺{{ number_format($stats['monthly_revenue'], 2) }}</div>
                        <small class="text-muted">Bu Ay Gelir</small>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="text-primary fw-bold fs-5">{{ $business->restaurants()->where('is_active', true)->count() }}</div>
                            <small class="text-muted">Aktif Restoran</small>
                        </div>
                        <div class="col-6">
                            <div class="text-info fw-bold fs-5">{{ $stats['total_staff'] }}</div>
                            <small class="text-muted">Toplam Personel</small>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Plan Durumu</small>
                                <small class="fw-bold">{{ strtoupper($business->plan) }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-{{ $business->plan === 'free' ? 'secondary' : 'success' }}" 
                                     style="width: {{ $business->plan === 'free' ? '25' : '75' }}%"></div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                Kayıt: {{ $business->created_at->format('d.m.Y') }}
                                <br>
                                ({{ $business->created_at->diffForHumans() }})
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Siparişler -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Son Siparişler
                        </h5>
                        <span class="badge bg-primary">{{ $recentOrders->count() }} sipariş</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Sipariş</th>
                                        <th>Restoran</th>
                                        <th>Masa</th>
                                        <th>Ürünler</th>
                                        <th>Durum</th>
                                        <th>Tutar</th>
                                        <th>Zaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#{{ $order->id }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $order->restaurant->name }}</div>
                                                <small class="text-muted">{{ $order->restaurant->slug }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $order->table_number }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($order->orderItems->take(2) as $item)
                                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                                            {{ $item->quantity }}x {{ Str::limit($item->product->name, 12) }}
                                                        </span>
                                                    @endforeach
                                                    @if($order->orderItems->count() > 2)
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ $order->orderItems->count() - 2 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Bekliyor</span>
                                                        @break
                                                    @case('preparing')
                                                        <span class="badge bg-info">Hazırlanıyor</span>
                                                        @break
                                                    @case('ready')
                                                        <span class="badge bg-success">Hazır</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge bg-primary">Teslim Edildi</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">İptal</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">₺{{ number_format($order->total, 2) }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $order->created_at->format('H:i') }}</div>
                                                <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 3rem;">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h5 class="text-muted">Henüz sipariş alınmamış</h5>
                            <p class="text-muted">İlk siparişin gelmesini bekliyoruz.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Otomatik sayfa yenileme (5 dakikada bir)
    setInterval(() => {
        location.reload();
    }, 300000);
</script>
@endsection 