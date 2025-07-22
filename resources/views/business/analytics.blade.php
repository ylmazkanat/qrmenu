@extends('layouts.business')

@section('title', 'İstatistikler - QR Menu')
@section('page-title', 'İstatistikler')

@section('content')
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stats-value">{{ $stats['total_restaurants'] }}</div>
                <div class="stats-label">Toplam Restoran</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    {{ $stats['active_restaurants'] }} aktif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-menu-app"></i>
                </div>
                <div class="stats-value">{{ $stats['total_products'] }}</div>
                <div class="stats-label">Toplam Ürün</div>
                <div class="stats-change positive">
                    <i class="bi bi-plus"></i>
                    Tüm restoranlar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-value">{{ $stats['total_orders'] }}</div>
                <div class="stats-label">Toplam Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    Tüm zamanlar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-value">{{ $stats['total_staff'] }}</div>
                <div class="stats-label">Toplam Personel</div>
                <div class="stats-change positive">
                    <i class="bi bi-person-check"></i>
                    Aktif çalışanlar
                </div>
            </div>
        </div>
    </div>

    <!-- Günlük Grafik -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Son 30 Gün - Sipariş ve Gelir Analizi
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Restoran Performansları -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-trophy me-2"></i>
                        Restoran Performansları
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Restoran</th>
                                    <th>Bugün Sipariş</th>
                                    <th>Bugün Gelir</th>
                                    <th>Toplam Sipariş</th>
                                    <th>Ortalama Sipariş</th>
                                    <th>Durum</th>
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
                                            <div class="fw-bold text-primary">{{ $restaurant->getTodayOrdersCount() }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-success">₺{{ number_format($restaurant->getTodayRevenue(), 2) }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $restaurant->orders->count() }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                @if($restaurant->orders->count() > 0)
                                                    ₺{{ number_format($restaurant->orders->avg('total'), 2) }}
                                                @else
                                                    ₺0.00
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($restaurant->is_active)
                                                <span class="badge badge-modern bg-success">Aktif</span>
                                            @else
                                                <span class="badge badge-modern bg-danger">Pasif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Özet Bilgiler -->
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Özet Bilgiler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Bu Ay Toplam Gelir:</span>
                            <strong class="text-success">₺{{ number_format($stats['monthly_revenue'], 2) }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Bu Ay Sipariş:</span>
                            <strong class="text-primary">{{ $stats['monthly_orders'] }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 65%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Ortalama Sipariş Tutarı:</span>
                            <strong class="text-info">
                                @if($stats['total_orders'] > 0)
                                    ₺{{ number_format($stats['monthly_revenue'] / $stats['monthly_orders'], 2) }}
                                @else
                                    ₺0.00
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="mb-3">En Popüler Ürünler</h6>
                        <div class="d-flex flex-column gap-2">
                            @php
                                $popularProducts = collect();
                                foreach($business->restaurants as $restaurant) {
                                    foreach($restaurant->products->take(3) as $product) {
                                        $popularProducts->push($product);
                                    }
                                }
                                $popularProducts = $popularProducts->take(5);
                            @endphp
                            
                            @foreach($popularProducts as $product)
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($product->name, 20) }}</div>
                                        <small class="text-muted">₺{{ number_format($product->price, 2) }}</small>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $product->orderItems->count() }} sipariş</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Günlük grafik
    const ctx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($dailyStats as $stat)
                    '{{ \Carbon\Carbon::parse($stat['date'])->format('d.m') }}',
                @endforeach
            ],
            datasets: [{
                label: 'Sipariş Sayısı',
                data: [
                    @foreach($dailyStats as $stat)
                        {{ $stat['orders'] }},
                    @endforeach
                ],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Gelir (₺)',
                data: [
                    @foreach($dailyStats as $stat)
                        {{ $stat['revenue'] }},
                    @endforeach
                ],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sipariş Sayısı'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Gelir (₺)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>
@endsection 