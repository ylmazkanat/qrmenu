@extends('layouts.business')

@section('title', 'İşletme Dashboard - QR Menu')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">Hoş geldiniz, {{ auth()->user()->name }}! 👋</h3>
                            <p class="text-muted mb-0">{{ $business->name }} işletmenizi buradan yönetebilirsiniz. Restoranlarınızı kontrol edin ve istatistikleri takip edin.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('business.restaurants.create') }}" class="btn btn-business-modern">
                                    <i class="bi bi-plus-circle"></i>
                                    Yeni Restoran
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
                <div class="stats-value">{{ $stats['today_orders'] }}</div>
                <div class="stats-label">Bugün Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    Günlük toplam
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
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
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('business.restaurants.create') }}" class="btn btn-outline-primary w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-plus-circle fs-1 mb-2"></i>
                                    <div class="fw-medium">Yeni Restoran</div>
                                    <small class="text-muted">Restoran ekle</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('business.restaurants') }}" class="btn btn-outline-success w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-shop fs-1 mb-2"></i>
                                    <div class="fw-medium">Restoranlarım</div>
                                    <small class="text-muted">Tümünü görüntüle</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('business.staff') }}" class="btn btn-outline-info w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-people fs-1 mb-2"></i>
                                    <div class="fw-medium">Çalışanlar</div>
                                    <small class="text-muted">Personel yönetimi</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('business.analytics') }}" class="btn btn-outline-warning w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-graph-up fs-1 mb-2"></i>
                                    <div class="fw-medium">İstatistikler</div>
                                    <small class="text-muted">Detaylı analiz</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Restaurants -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="bi bi-clock-history me-2"></i>
                            Restoranlarım
                        </h5>
                        <a href="{{ route('business.restaurants') }}" class="btn btn-sm btn-outline-primary">
                            Tümünü Gör
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentRestaurants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Restoran</th>
                                        <th>Durum</th>
                                        <th>Siparişler</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRestaurants as $restaurant)
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
                                                @if($restaurant->is_active)
                                                    <span class="badge badge-modern bg-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-modern bg-danger">Pasif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-primary">{{ $restaurant->orders->count() }}</div>
                                                    <small class="text-muted">Toplam</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('menu.show', $restaurant->slug) }}" 
                                                       class="btn btn-outline-primary" target="_blank" 
                                                       data-bs-toggle="tooltip" title="Menüyü Görüntüle">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('business.restaurants.show', $restaurant) }}" 
                                                       class="btn btn-outline-info" 
                                                       data-bs-toggle="tooltip" title="Detayları Gör">
                                                        <i class="bi bi-info-circle"></i>
                                                    </a>
                                                </div>
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
                            <h5 class="text-muted">Henüz restoran eklenmemiş</h5>
                            <p class="text-muted">İlk restoranınızı oluşturun ve QR menü sistemini kullanmaya başlayın.</p>
                            <a href="{{ route('business.restaurants.create') }}" class="btn btn-business-modern mt-3">
                                <i class="bi bi-plus-circle"></i>
                                İlk Restoranı Oluştur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Business Info -->
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-building me-2"></i>
                        İşletme Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>İşletme:</strong><br>
                        <span class="text-muted">{{ $business->name }}</span>
                    </div>
                    
                    @if($business->description)
                        <div class="mb-3">
                            <strong>Açıklama:</strong><br>
                            <span class="text-muted">{{ $business->description }}</span>
                        </div>
                    @endif
                    
                    @if($business->phone)
                        <div class="mb-3">
                            <strong>Telefon:</strong><br>
                            <span class="text-muted">{{ $business->phone }}</span>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Plan:</strong><br>
                        <span class="badge badge-modern bg-{{ $business->plan == 'free' ? 'secondary' : 'success' }}">
                            {{ ucfirst($business->plan) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Oluşturma:</strong><br>
                        <span class="text-muted">{{ $business->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary fw-bold fs-5">{{ $stats['active_restaurants'] }}/{{ $stats['total_restaurants'] }}</div>
                                <small class="text-muted">Aktif Restoran</small>
                            </div>
                            <div class="col-6">
                                <div class="text-success fw-bold fs-5">{{ $stats['total_orders'] }}</div>
                                <small class="text-muted">Toplam Sipariş</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 