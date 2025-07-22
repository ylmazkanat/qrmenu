@extends('layouts.business')

@section('title', 'Restoran Detayı - QR Menu')
@section('page-title', $restaurant->name . ' - Detaylar')

@section('content')
    <!-- Restoran Bilgi Başlığı -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            @if($restaurant->logo)
                                <img src="{{ Storage::url($restaurant->logo) }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="bi bi-shop fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-2">{{ $restaurant->name }}</h3>
                            @if($restaurant->description)
                                <p class="text-muted mb-2">{{ $restaurant->description }}</p>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    @if($restaurant->phone)
                                        <div class="mb-1">
                                            <i class="bi bi-telephone me-2"></i>
                                            {{ $restaurant->phone }}
                                        </div>
                                    @endif
                                    <div class="mb-1">
                                        <i class="bi bi-link-45deg me-2"></i>
                                        <code>{{ $restaurant->slug }}</code>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <i class="bi bi-table me-2"></i>
                                        {{ $restaurant->table_count }} masa
                                    </div>
                                    <div class="mb-1">
                                        @if($restaurant->is_active)
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
                        <div class="col-md-4 text-end">
                            <a href="{{ route('business.restaurants.edit', $restaurant->id) }}" class="btn btn-business-modern">
                                <i class="bi bi-pencil-square"></i> Düzenle
                            </a>
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
                    <i class="bi bi-list-ul"></i>
                </div>
                <div class="stats-value">{{ $stats['total_categories'] }}</div>
                <div class="stats-label">Kategori</div>
                <div class="stats-change neutral">
                    <i class="bi bi-dash"></i>
                    Menü kategorisi
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
                    Menü ürünü
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
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
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-value">{{ $stats['today_orders'] }}</div>
                <div class="stats-label">Bugün Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    Günlük
                </div>
            </div>
        </div>
    </div>

    <!-- Günlük Performans -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Bugün Performans
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="mb-3">
                                <div class="text-primary fw-bold fs-4">{{ $stats['today_orders'] }}</div>
                                <small class="text-muted">Bugün Sipariş</small>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="mb-3">
                                <div class="text-success fw-bold fs-4">₺{{ number_format($stats['today_revenue'], 2) }}</div>
                                <small class="text-muted">Bugün Gelir</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="mb-3">
                                <div class="text-info fw-bold fs-5">{{ $restaurant->categories()->count() }}</div>
                                <small class="text-muted">Kategori</small>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="mb-3">
                                <div class="text-warning fw-bold fs-5">{{ $restaurant->products()->count() }}</div>
                                <small class="text-muted">Ürün</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($stats['today_orders'] > 0)
                        <div class="border-top pt-3 text-center">
                            <div class="text-muted">Ortalama Sipariş Tutarı</div>
                            <div class="fw-bold text-primary fs-5">
                                ₺{{ number_format($stats['today_revenue'] / $stats['today_orders'], 2) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-people me-2"></i>
                        Personel Durumu
                    </h5>
                </div>
                <div class="card-body">
                    @if($restaurant->staff()->where('is_active', true)->count() > 0)
                        @foreach($restaurant->staff()->where('is_active', true)->with('user')->get() as $staff)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    @php
                                        $bgColor = match($staff->role) {
                                            'restaurant_manager' => 'danger',
                                            'waiter' => 'primary',
                                            'kitchen' => 'warning',
                                            'cashier' => 'success',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <div class="bg-{{ $bgColor }} rounded text-white d-flex align-items-center justify-content-center me-3" 
                                         style="width: 35px; height: 35px; font-size: 12px;">
                                        {{ substr($staff->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $staff->user->name }}</div>
                                        <small class="text-muted">
                                            {{ match($staff->role) {
                                                'restaurant_manager' => 'Müdür',
                                                'waiter' => 'Garson',
                                                'kitchen' => 'Mutfak',
                                                'cashier' => 'Kasiyer',
                                                default => 'Diğer'
                                            } }}
                                        </small>
                                    </div>
                                </div>
                                <span class="badge badge-modern bg-success">Aktif</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-people fs-3"></i>
                            <p class="mt-2">Henüz personel atanmamış</p>
                        </div>
                    @endif
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
                                                <span class="badge bg-light text-dark">{{ $order->table_number }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($order->orderItems->take(3) as $item)
                                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                                            {{ $item->quantity }}x {{ Str::limit($item->product->name, 15) }}
                                                        </span>
                                                    @endforeach
                                                    @if($order->orderItems->count() > 3)
                                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ $order->orderItems->count() - 3 }}</span>
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
    // Otomatik sayfa yenileme (3 dakikada bir)
    setInterval(() => {
        location.reload();
    }, 180000);
</script>
@endsection 