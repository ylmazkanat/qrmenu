@extends('layouts.restaurant')

@section('title', 'Restoran Dashboard - QR Menu')
@section('page-title', 'Restoran Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">Hoş geldiniz, {{ auth()->user()->name }}! 👋</h3>
                            <p class="text-muted mb-0">{{ $restaurant->name ?? 'Restoran' }} restoranının operasyonel durumunu buradan takip edebilirsiniz.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('menu.show', $restaurant->slug ?? '') }}" class="btn btn-restaurant-modern" target="_blank">
                                    <i class="bi bi-eye"></i>
                                    Menüyü Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-value">{{ $stats['today_orders'] }}</div>
                <div class="stats-label">Bugün Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    +{{ $stats['yesterday_orders'] }} dün
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stats-value">₺{{ number_format($stats['today_revenue'], 2) }}</div>
                <div class="stats-label">Bugün Ciro</div>
                <div class="stats-change positive">
                    <i class="bi bi-graph-up"></i>
                    Günlük gelir
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-value">{{ $stats['active_orders'] }}</div>
                <div class="stats-label">Aktif Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-activity"></i>
                    Devam eden
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-value">{{ $stats['active_staff'] }}</div>
                <div class="stats-label">Aktif Personel</div>
                <div class="stats-change positive">
                    <i class="bi bi-person-check"></i>
                    Çalışan
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Hızlı İşlemler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->isRestaurantManager() || auth()->user()->isWaiter())
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('restaurant.waiter') }}" class="btn btn-outline-primary w-100 p-3">
                                    <div class="text-center">
                                        <i class="bi bi-person-badge fs-1 mb-2"></i>
                                        <div class="fw-medium">Sipariş Al</div>
                                        <small class="text-muted">Garson paneli</small>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->isRestaurantManager() || auth()->user()->isKitchen())
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('restaurant.kitchen') }}" class="btn btn-outline-warning w-100 p-3">
                                    <div class="text-center">
                                        <i class="bi bi-fire fs-1 mb-2"></i>
                                        <div class="fw-medium">Mutfak Paneli</div>
                                        <small class="text-muted">Sipariş hazırlama</small>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->isRestaurantManager() || auth()->user()->isCashier())
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('restaurant.cashier') }}" class="btn btn-outline-success w-100 p-3">
                                    <div class="text-center">
                                        <i class="bi bi-cash-coin fs-1 mb-2"></i>
                                        <div class="fw-medium">Ödeme Al</div>
                                        <small class="text-muted">Kasiyer paneli</small>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->isRestaurantManager())
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('restaurant.menu.management') }}" class="btn btn-outline-dark w-100 p-3">
                                    <div class="text-center">
                                        <i class="bi bi-gear fs-1 mb-2"></i>
                                        <div class="fw-medium">Menü Yönetimi</div>
                                        <small class="text-muted">Kategori & Ürün</small>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('menu.show', $restaurant->slug) }}" class="btn btn-outline-info w-100 p-3" target="_blank">
                                <div class="text-center">
                                    <i class="bi bi-qr-code fs-1 mb-2"></i>
                                    <div class="fw-medium">QR Menü</div>
                                    <small class="text-muted">Müşteri görünümü</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktif Siparişler ve Son Aktiviteler -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Aktif Siparişler
                        </h5>
                        <span class="badge bg-primary">{{ $activeOrders ? $activeOrders->count() : 0 }} aktif</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($activeOrders && $activeOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Masa</th>
                                        <th>Müşteri</th>
                                        <th>Sipariş Zamanı</th>
                                        <th>Durum</th>
                                        <th>Tutar</th>
                                        <th>Süre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">Masa {{ $order->table_number }}</span>
                                            </td>
                                            <td>
                                                @if($order->customer_name)
                                                    <span class="text-primary">{{ $order->customer_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('H:i') }}</td>
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
                                                    @default
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>₺{{ number_format($order->total, 2) }}</td>
                                            <td>
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
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h5 class="text-muted">Aktif sipariş bulunmuyor</h5>
                            <p class="text-muted">Tüm siparişler tamamlanmış durumda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Restoran Bilgileri -->
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-shop me-2"></i>
                        Restoran Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Restoran:</strong><br>
                        <span class="text-muted">{{ $restaurant->name ?? 'Belirtilmemiş' }}</span>
                    </div>
                    
                    @if($restaurant->description ?? null)
                        <div class="mb-3">
                            <strong>Açıklama:</strong><br>
                            <span class="text-muted">{{ $restaurant->description }}</span>
                        </div>
                    @endif
                    
                    @if($restaurant->phone ?? null)
                        <div class="mb-3">
                            <strong>Telefon:</strong><br>
                            <span class="text-muted">{{ $restaurant->phone }}</span>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Masa Sayısı:</strong><br>
                        <span class="text-muted">{{ $restaurant->table_count ?? 0 }} masa</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Durum:</strong><br>
                        @if($restaurant->is_active ?? false)
                            <span class="badge badge-modern bg-success">Aktif</span>
                        @else
                            <span class="badge badge-modern bg-danger">Pasif</span>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary fw-bold fs-5">{{ $restaurant->categories->count() ?? 0 }}</div>
                                <small class="text-muted">Kategori</small>
                            </div>
                            <div class="col-6">
                                <div class="text-success fw-bold fs-5">{{ $restaurant->products->count() ?? 0 }}</div>
                                <small class="text-muted">Ürün</small>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isRestaurantManager())
                        <div class="border-top pt-3 mt-3">
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="bi bi-gear"></i>
                                Restoran Ayarları
                            </a>
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
                    <h5 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Son Siparişler (Bugün)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders && $recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Masa</th>
                                        <th>Müşteri</th>
                                        <th>Sipariş Zamanı</th>
                                        <th>Ürünler</th>
                                        <th>Durum</th>
                                        <th>Tutar</th>
                                        <th>Süre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">Masa {{ $order->table_number }}</span>
                                            </td>
                                            <td>
                                                @if($order->customer_name)
                                                    <span class="text-primary">{{ $order->customer_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('H:i') }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($order->orderItems->take(3) as $item)
                                                        @if($item->product)
                                                            <span class="badge bg-light text-dark">
                                                                {{ $item->quantity }}x {{ Str::limit($item->product->name, 15) }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-dark">
                                                                {{ $item->quantity }}x Silinmiş Ürün
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                    @if($order->orderItems->count() > 3)
                                                        <span class="badge bg-secondary">+{{ $order->orderItems->count() - 3 }}</span>
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
                                            <td>₺{{ number_format($order->total, 2) }}</td>
                                            <td>
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
                            <h5 class="text-muted">Bugün henüz sipariş alınmamış</h5>
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
    // Otomatik sayfa yenileme (2 dakikada bir)
    setInterval(() => {
        location.reload();
    }, 120000);

    // Real-time istatistik güncellemesi
    function updateStats() {
        fetch('{{ route("restaurant.api.orders.updates") }}')
            .then(response => response.json())
            .then(data => {
                // İstatistikleri güncelle
                document.querySelector('.stats-card:nth-child(3) .stats-value').textContent = data.pending_orders + data.preparing_orders + data.ready_orders;
            })
            .catch(error => {
                console.error('İstatistik güncelleme hatası:', error);
            });
    }

    // Her 30 saniyede bir istatistikleri güncelle
    setInterval(updateStats, 30000);
</script>
@endsection 