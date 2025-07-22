@extends('layouts.admin')

@section('title', 'İstatistikler - QR Menu Admin')
@section('page-title', 'Sistem İstatistikleri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">İstatistikler</li>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">Sistem İstatistikleri</h3>
            <p class="text-muted mb-0">Detaylı sistem performansı ve kullanım raporları</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button class="btn btn-primary-modern">
                    <i class="bi bi-download"></i>
                    Rapor İndir
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-value">{{ $stats['total_users'] }}</div>
                <div class="stats-label">Toplam Kullanıcı</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Bu ay +{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stats-value">{{ $stats['total_restaurants'] }}</div>
                <div class="stats-label">Toplam Restoran</div>
                <div class="stats-change positive">
                    <i class="bi bi-check-circle"></i>
                    {{ $stats['active_restaurants'] }} aktif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stats-value">{{ $stats['total_products'] }}</div>
                <div class="stats-label">Toplam Ürün</div>
                <div class="stats-change positive">
                    <i class="bi bi-graph-up"></i>
                    Ortalama {{ $stats['total_restaurants'] > 0 ? round($stats['total_products'] / $stats['total_restaurants'], 1) : 0 }} ürün/restoran
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stats-value">{{ $stats['total_orders'] }}</div>
                <div class="stats-label">Toplam Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Bugün +{{ \App\Models\Order::whereDate('created_at', today())->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- User Growth Chart -->
        <div class="col-lg-6 mb-4">
            <div class="content-card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Kullanıcı Artışı (Son 7 Gün)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Restaurant Status Chart -->
        <div class="col-lg-6 mb-4">
            <div class="content-card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-pie-chart me-2"></i>
                        Restoran Durumu
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="restaurantStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Row -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-speedometer me-2"></i>
                        Performans
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Server Status</span>
                            <span class="badge badge-modern bg-success">Online</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Disk Kullanımı</span>
                            <span class="text-muted">45%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 45%"></div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-success fw-bold fs-4">99.9%</div>
                                <small class="text-muted">Uptime</small>
                            </div>
                            <div class="col-6">
                                <div class="text-info fw-bold fs-4">185ms</div>
                                <small class="text-muted">Avg Response</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-activity me-2"></i>
                        Haftalık Aktivite
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Yeni Üyeler</span>
                            <span class="text-primary fw-bold">{{ \App\Models\User::where('created_at', '>=', now()->subDays(7))->count() }}</span>
                        </div>
                        <small class="text-muted">Son 7 günde katılan kullanıcılar</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Yeni Siparişler</span>
                            <span class="text-warning fw-bold">{{ \App\Models\Order::where('created_at', '>=', now()->subDays(7))->count() }}</span>
                        </div>
                        <small class="text-muted">Son 7 günde verilen siparişler</small>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary fw-bold fs-4">{{ \App\Models\Restaurant::where('created_at', '>=', now()->subDays(7))->count() }}</div>
                                <small class="text-muted">Yeni Restoran</small>
                            </div>
                            <div class="col-6">
                                <div class="text-success fw-bold fs-4">{{ \App\Models\Product::where('created_at', '>=', now()->subDays(7))->count() }}</div>
                                <small class="text-muted">Yeni Ürün</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-star me-2"></i>
                        Sistem Kalitesi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Bellek Kullanımı</span>
                            <span class="text-muted">62%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 62%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Veritabanı</span>
                            <span class="badge badge-modern bg-success">Bağlı</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-success fw-bold fs-4">4.8</div>
                                <small class="text-muted">Ortalama Puan</small>
                            </div>
                            <div class="col-6">
                                <div class="text-info fw-bold fs-4">98%</div>
                                <small class="text-muted">Memnuniyet</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title">
                                <i class="bi bi-clock-history me-2"></i>
                                Son Siparişler
                            </h5>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">Son 10 sipariş</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($stats['recent_orders']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Sipariş #</th>
                                        <th>Restoran</th>
                                        <th>Masa</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Toplam</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_orders'] as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $order->restaurant->name }}</div>
                                                    <small class="text-muted">{{ $order->restaurant->slug }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($order->table_number)
                                                    <span class="badge badge-modern bg-light text-dark">
                                                        <i class="bi bi-table"></i> {{ $order->table_number }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-info fw-bold">{{ $order->orderItems->count() }}</span>
                                                <small class="text-muted">ürün</small>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">{{ number_format($order->total, 2) }} ₺</span>
                                            </td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge badge-modern bg-warning">
                                                        <i class="bi bi-clock"></i> Bekliyor
                                                    </span>
                                                @elseif($order->status == 'preparing')
                                                    <span class="badge badge-modern bg-info">
                                                        <i class="bi bi-gear"></i> Hazırlanıyor
                                                    </span>
                                                @elseif($order->status == 'ready')
                                                    <span class="badge badge-modern bg-primary">
                                                        <i class="bi bi-check"></i> Hazır
                                                    </span>
                                                @elseif($order->status == 'delivered')
                                                    <span class="badge badge-modern bg-success">
                                                        <i class="bi bi-check-circle"></i> Teslim Edildi
                                                    </span>
                                                @else
                                                    <span class="badge badge-modern bg-danger">
                                                        <i class="bi bi-x-circle"></i> İptal
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $order->created_at->format('d.m.Y') }}</div>
                                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 4rem;">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h4 class="text-muted mb-2">Henüz sipariş bulunmuyor</h4>
                            <p class="text-muted">Sistemde henüz sipariş verilmemiş</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['6 gün önce', '5 gün önce', '4 gün önce', '3 gün önce', '2 gün önce', 'Dün', 'Bugün'],
            datasets: [{
                label: 'Yeni Kullanıcılar',
                data: [2, 4, 3, 5, 8, 6, 7],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#4f46e5',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        borderColor: '#e2e8f0'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Restaurant Status Chart
    const restaurantStatusCtx = document.getElementById('restaurantStatusChart').getContext('2d');
    new Chart(restaurantStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif Restoranlar', 'Pasif Restoranlar'],
            datasets: [{
                data: [{{ $stats['active_restaurants'] }}, {{ $stats['total_restaurants'] - $stats['active_restaurants'] }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            family: 'Inter',
                            size: 14
                        },
                        color: '#64748b'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#334155',
                    borderWidth: 1,
                    cornerRadius: 8,
                    titleFont: {
                        family: 'Inter',
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        family: 'Inter',
                        size: 13
                    }
                }
            }
        }
    });
</script>
@endpush 