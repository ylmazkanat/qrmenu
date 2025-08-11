@extends('layouts.admin')

@section('title', 'Admin İstatistikleri - QR Menu Admin')
@section('page-title', 'Admin İstatistikleri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Admin İstatistikleri</li>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 15px;
    color: white;
    transition: transform 0.3s ease;
}
.stats-card:hover {
    transform: translateY(-5px);
}
.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 15px;
}
.gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.gradient-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%); }
.gradient-info { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }
.gradient-warning { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }
.gradient-danger { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }
.gradient-purple { background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Admin İstatistikleri</h1>
            <p class="mb-0 text-muted">Sistem yönetimi ve gelir istatistikleri</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="showStats('overview')">Genel Bakış</button>
                <button type="button" class="btn btn-outline-primary" onclick="showStats('packages')">Paket İstatistikleri</button>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row mb-4" id="overview-stats">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-primary shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-primary mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-sm opacity-75">Toplam Üye</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-success shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-success mx-auto">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['total_businesses']) }}</div>
                    <div class="text-sm opacity-75">Toplam İşletme</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-info shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-info mx-auto">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['active_subscriptions']) }}</div>
                    <div class="text-sm opacity-75">Aktif Abonelik</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-warning shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-warning mx-auto">
                        <i class="fas fa-lira-sign"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">₺{{ number_format($stats['monthly_subscription_revenue'], 0) }}</div>
                    <div class="text-sm opacity-75">Bu Ay Gelir</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-danger shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-danger mx-auto">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">₺{{ number_format($stats['total_subscription_revenue'], 0) }}</div>
                    <div class="text-sm opacity-75">Toplam Gelir</div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card stats-card gradient-purple shadow h-100 py-3">
                <div class="card-body text-center">
                    <div class="stats-icon gradient-purple mx-auto">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['active_restaurants']) }}</div>
                    <div class="text-sm opacity-75">Aktif Restoran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Subscription Revenue Chart -->
        <div class="col-lg-6 mb-4">
            <div class="content-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Abonelik Geliri
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="updateRevenueChart('monthly')">Aylık</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateRevenueChart('quarterly')">3 Aylık</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateRevenueChart('halfyearly')">6 Aylık</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateRevenueChart('yearly')">Yıllık</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="subscriptionRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Package Usage Chart -->
        <div class="col-lg-6 mb-4">
            <div class="content-card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-pie-chart me-2"></i>
                        Paket Kullanımı
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="packageUsageChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Statistics Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-box me-2"></i>
                        Paket İstatistikleri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($packageUsage as $package)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $package->name }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $package->active_subscriptions }}</div>
                                            <div class="text-xs text-muted">Aktif Abonelik</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions Table -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title">
                                <i class="bi bi-box me-2"></i>
                                Son Alınan Paketler
                            </h5>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">Son 10 abonelik</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($stats['recent_subscriptions']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Abonelik #</th>
                                        <th>İşletme</th>
                                        <th>Paket</th>
                                        <th>Ödenen Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme Tarihi</th>
                                        <th>Bitiş Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_subscriptions'] as $subscription)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $subscription->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $subscription->business->name }}</div>
                                                    <small class="text-muted">{{ $subscription->business->slug }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-modern bg-light text-dark">
                                                    <i class="bi bi-box"></i> {{ $subscription->package->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">{{ number_format($subscription->amount_paid, 2) }} ₺</span>
                                            </td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                    <span class="badge badge-modern bg-success">
                                                        <i class="bi bi-check-circle"></i> Aktif
                                                    </span>
                                                @elseif($subscription->status == 'inactive')
                                                    <span class="badge badge-modern bg-warning">
                                                        <i class="bi bi-pause-circle"></i> Pasif
                                                    </span>
                                                @elseif($subscription->status == 'expired')
                                                    <span class="badge badge-modern bg-danger">
                                                        <i class="bi bi-x-circle"></i> Süresi Dolmuş
                                                    </span>
                                                @elseif($subscription->status == 'cancelled')
                                                    <span class="badge badge-modern bg-secondary">
                                                        <i class="bi bi-dash-circle"></i> İptal
                                                    </span>
                                                @else
                                                    <span class="badge badge-modern bg-info">
                                                        <i class="bi bi-info-circle"></i> {{ $subscription->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    @if($subscription->payment_date)
                                                        <div class="fw-medium">{{ $subscription->payment_date->format('d.m.Y') }}</div>
                                                        <small class="text-muted">{{ $subscription->payment_date->format('H:i') }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($subscription->expires_at)
                                                        <div class="fw-medium">{{ $subscription->expires_at->format('d.m.Y') }}</div>
                                                        @if($subscription->expires_at->isPast())
                                                            <small class="text-danger">Süresi dolmuş</small>
                                                        @else
                                                            <small class="text-success">{{ $subscription->expires_at->diffForHumans() }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
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
                                <i class="bi bi-box"></i>
                            </div>
                            <h4 class="text-muted mb-2">Henüz paket aboneliği bulunmuyor</h4>
                            <p class="text-muted">Sistemde henüz paket satın alınmamış</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Subscription data for different periods
const subscriptionData = {
    monthly: {
        labels: {!! json_encode(array_column($subscriptionStats['monthly'], 'date')) !!},
        revenue: {!! json_encode(array_column($subscriptionStats['monthly'], 'revenue')) !!},
        subscriptions: {!! json_encode(array_column($subscriptionStats['monthly'], 'subscriptions')) !!}
    },
    quarterly: {
        labels: {!! json_encode(array_column($subscriptionStats['quarterly'], 'date')) !!},
        revenue: {!! json_encode(array_column($subscriptionStats['quarterly'], 'revenue')) !!},
        subscriptions: {!! json_encode(array_column($subscriptionStats['quarterly'], 'subscriptions')) !!}
    },
    halfyearly: {
        labels: {!! json_encode(array_column($subscriptionStats['halfyearly'], 'date')) !!},
        revenue: {!! json_encode(array_column($subscriptionStats['halfyearly'], 'revenue')) !!},
        subscriptions: {!! json_encode(array_column($subscriptionStats['halfyearly'], 'subscriptions')) !!}
    },
    yearly: {
        labels: {!! json_encode(array_column($subscriptionStats['yearly'], 'date')) !!},
        revenue: {!! json_encode(array_column($subscriptionStats['yearly'], 'revenue')) !!},
        subscriptions: {!! json_encode(array_column($subscriptionStats['yearly'], 'subscriptions')) !!}
    }
};

// Subscription Revenue Chart
const subscriptionRevenueCtx = document.getElementById('subscriptionRevenueChart').getContext('2d');
let subscriptionRevenueChart = new Chart(subscriptionRevenueCtx, {
    type: 'line',
    data: {
        labels: subscriptionData.monthly.labels,
        datasets: [{
            label: 'Abonelik Geliri (₺)',
            data: subscriptionData.monthly.revenue,
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Yeni Abonelikler',
            data: subscriptionData.monthly.subscriptions,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            title: {
                display: true,
                text: 'Son 30 Günün Abonelik İstatistikleri'
            },
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Tarih'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Gelir (₺)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Abonelik Sayısı'
                },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Package Usage Chart
const packageUsageCtx = document.getElementById('packageUsageChart').getContext('2d');
const packageUsageChart = new Chart(packageUsageCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($packageUsage->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($packageUsage->pluck('active_subscriptions')) !!},
            backgroundColor: [
                '#667eea',
                '#56ab2f', 
                '#3498db',
                '#f39c12',
                '#e74c3c',
                '#9b59b6',
                '#1abc9c',
                '#34495e'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} abonelik (${percentage}%)`;
                    }
                }
            }
        }
    }
});

function updateRevenueChart(period) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart data
    const data = subscriptionData[period];
    subscriptionRevenueChart.data.labels = data.labels;
    subscriptionRevenueChart.data.datasets[0].data = data.revenue;
    subscriptionRevenueChart.data.datasets[1].data = data.subscriptions;
    subscriptionRevenueChart.update();
}

function showStats(type) {
    // Toggle active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Show/hide content based on type
    if (type === 'overview') {
        document.getElementById('overview-stats').style.display = 'block';
        // Add other overview elements here
    } else if (type === 'packages') {
        document.getElementById('overview-stats').style.display = 'block';
        // Add package view logic here
    }
}
</script>
@endpush