@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Paket Yönetimi</h1>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Paket Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mevcut Paketler</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="packagesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Paket Adı & Açıklama</th>
                            <th>Fiyat & Döngü</th>
                            <th>Özellikler (Aktif/Toplam)</th>
                            <th>Aktif Abonelikler</th>
                            <th>Durum</th>
                            <th>Sıralama</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                        <tr>
                            <td>{{ $package->id }}</td>
                            <td>
                                <strong>{{ $package->name }}</strong> @if($package->is_popular) <span class="badge bg-warning text-dark">Popüler</span> @endif
                                <br><small class="text-muted">{{ $package->description ?? 'Açıklama yok' }}</small>
                            </td>
                            <td>
                                {{ number_format($package->price, 2) }} ₺
                                <br>
                                <span class="badge @if($package->billing_cycle == 'monthly') bg-info @else bg-success @endif">{{ ucfirst($package->billing_cycle) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $package->enabled_features_count ?? 0 }} / {{ $package->total_features_count ?? 0 }}</span>
                                <button class="btn btn-sm btn-link" data-bs-toggle="collapse" data-bs-target="#features-{{ $package->id }}">Detay</button>
                                <div class="collapse" id="features-{{ $package->id }}">
                                    <ul class="list-group list-group-flush small">
                                        @foreach($package->packageFeatures ?? [] as $feature)
                                            <li class="list-group-item @if($feature->is_enabled) text-success @else text-muted @endif">
                                                {{ $feature->feature_name }} @if($feature->limit_value && $feature->limit_value > 0) : {{ $feature->limit_value }} @elseif($feature->isUnlimited()) : Sınırsız @elseif($feature->is_enabled) : Etkin @else : Kapalı @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <span class="badge @if(($package->active_subscriptions_count ?? 0) > 0) bg-success @else bg-secondary @endif">{{ $package->active_subscriptions_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge @if($package->is_active) bg-success @else bg-secondary @endif">{{ $package->is_active ? 'Aktif' : 'Pasif' }}</span>
                            </td>
                            <td>{{ $package->sort_order }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istiyor musunuz?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Bölümler -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Abonelik Logları ve Ödemeler</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>İşletme</th>
                                <th>Paket</th>
                                <th>Abonelik Tarihi</th>
                                <th>Son Geçerlilik</th>
                                <th>Ödeme Tarihi</th>
                                <th>Tutar</th>
                                <th>Ödeme Yöntemi</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $sub)
                            <tr>
                                <td>{{ $sub->business->name ?? 'Bilinmiyor' }}</td>
                                <td>{{ $sub->package->name ?? 'Bilinmiyor' }}</td>
                                <td>{{ $sub->started_at }}</td>
                                <td>{{ $sub->expires_at }}</td>
                                <td>{{ $sub->payment_date }}</td>
                                <td>{{ $sub->amount_paid }} ₺</td>
                                <td>{{ $sub->payment_method ?? 'Belirtilmemiş' }}</td>
                                <td>
                                    @if($sub->status == 'cancelled')
                                        <span class="badge bg-danger">İptal Edildi</span>
                                    @elseif($sub->is_paid)
                                        <span class="badge bg-success">Ödendi</span>
                                    @elseif($sub->is_active)
                                        <span class="badge bg-warning">Ödeme Bekleniyor</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
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
</div>

<!-- Kazanç Özeti -->
<div class="row">
    <div class="col-md-3">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Toplam Kazanç</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEarnings, 2) }} ₺</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aktif Abonelikler</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('is_active', true)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow mb-4 border-left-info">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bu Ay Kazanç</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($monthlyEarnings->first()->total ?? 0, 2) }} ₺</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bekleyen Ödemeler</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('is_active', true)->where('is_paid', false)->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Büyük Kazanç Grafiği -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Kazanç Analizi</h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="dailyBtn" onclick="showChart('daily')">Günlük</button>
                    <button type="button" class="btn btn-outline-primary btn-sm active" id="monthlyBtn" onclick="showChart('monthly')">Aylık</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="annualBtn" onclick="showChart('annual')">Yıllık</button>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 400px;">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let earningsChart;
const chartData = {
    daily: {
        labels: {!! json_encode($chartData['daily']->pluck('label')) !!},
        data: {!! json_encode($chartData['daily']->pluck('value')) !!},
        label: 'Günlük Kazanç',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)'
    },
    monthly: {
        labels: {!! json_encode($chartData['monthly']->pluck('label')) !!},
        data: {!! json_encode($chartData['monthly']->pluck('value')) !!},
        label: 'Aylık Kazanç',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)'
    },
    annual: {
        labels: {!! json_encode($chartData['annual']->pluck('label')) !!},
        data: {!! json_encode($chartData['annual']->pluck('value')) !!},
        label: 'Yıllık Kazanç',
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgba(153, 102, 255, 1)'
    }
};

function showChart(period) {
    // Buton durumlarını güncelle
    document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
    document.getElementById(period + 'Btn').classList.add('active');
    
    // Grafik verilerini güncelle
    const data = chartData[period];
    earningsChart.data.labels = data.labels;
    earningsChart.data.datasets[0].data = data.data;
    earningsChart.data.datasets[0].label = data.label;
    earningsChart.data.datasets[0].backgroundColor = data.backgroundColor;
    earningsChart.data.datasets[0].borderColor = data.borderColor;
    earningsChart.update();
}

$(document).ready(function() {
    $('#packagesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
        },
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });
    
    // Ana Kazanç Grafiği
    const ctx = document.getElementById('earningsChart').getContext('2d');
    earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.monthly.labels,
            datasets: [{
                label: 'Aylık Kazanç',
                data: chartData.monthly.data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY'
                            }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Dönem'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Kazanç (₺)'
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY'
                            }).format(value);
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});
</script>
@endsection