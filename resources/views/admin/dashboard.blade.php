@extends('layouts.admin')

@section('title', 'Admin Dashboard - QR Menu')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">Hoş geldiniz, {{ auth()->user()->name }}! 👋</h3>
                            <p class="text-muted mb-0">QR Menu admin paneline hoş geldiniz. Sistem durumunu ve performansını buradan takip edebilirsiniz.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.analytics') }}" class="btn btn-primary-modern">
                                    <i class="bi bi-graph-up"></i>
                                    Detaylı Rapor
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
                    <i class="bi bi-building"></i>
                </div>
                <div class="stats-value">{{ $stats['total_businesses'] }}</div>
                <div class="stats-label">Toplam İşletme</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    {{ $stats['active_businesses'] }} aktif
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
                    <i class="bi bi-plus"></i>
                    {{ $stats['active_restaurants'] }} aktif
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-value">{{ $stats['total_users'] }}</div>
                <div class="stats-label">Toplam Kullanıcı</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Sistem kullanıcıları
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-value">{{ $stats['today_orders'] }}</div>
                <div class="stats-label">Bugün Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    ₺{{ number_format($stats['today_revenue'], 2) }} ciro
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
                            <a href="{{ route('admin.businesses') }}" class="btn btn-outline-primary w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-building fs-1 mb-2"></i>
                                    <div class="fw-medium">İşletme Yönetimi</div>
                                    <small class="text-muted">Tüm işletmeleri görüntüle</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.restaurants') }}" class="btn btn-outline-success w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-shop fs-1 mb-2"></i>
                                    <div class="fw-medium">Restoran Yönetimi</div>
                                    <small class="text-muted">Tüm restoranları gör</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-info w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-people fs-1 mb-2"></i>
                                    <div class="fw-medium">Kullanıcı Yönetimi</div>
                                    <small class="text-muted">Kullanıcıları yönet</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.analytics') }}" class="btn btn-outline-warning w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-graph-up fs-1 mb-2"></i>
                                    <div class="fw-medium">Sistem İstatistikleri</div>
                                    <small class="text-muted">Detaylı analiz</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="bi bi-building me-2"></i>
                            Son Eklenen İşletmeler
                        </h5>
                        <a href="{{ route('admin.businesses') }}" class="btn btn-sm btn-outline-primary">
                            Tümünü Gör
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentBusinesses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>İşletme</th>
                                        <th>Sahibi</th>
                                        <th>Restoranlar</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBusinesses as $business)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($business->logo)
                                                            <img src="{{ Storage::url($business->logo) }}" 
                                                                 class="rounded" width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="bi bi-building"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $business->name }}</div>
                                                        <small class="text-muted">{{ $business->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $business->owner->name }}</div>
                                                    <small class="text-muted">{{ $business->owner->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-primary">{{ $business->restaurants_count ?? 0 }}</div>
                                                    <small class="text-muted">Restoran</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($business->is_active)
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
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 3rem;">
                                <i class="bi bi-building"></i>
                            </div>
                            <h5 class="text-muted">Henüz işletme eklenmemiş</h5>
                            <p class="text-muted">Kullanıcılar henüz işletme oluşturmamış.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <i class="bi bi-people me-2"></i>
                            Son Kayıt Olan Kullanıcılar
                        </h5>
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">
                            Tümünü Gör
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Kullanıcı</th>
                                        <th>Rol</th>
                                        <th>Kayıt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @php
                                                            $bgColor = match($user->role) {
                                                                'admin' => 'danger',
                                                                'business_owner' => 'success',
                                                                'restaurant_manager' => 'info',
                                                                'waiter' => 'primary',
                                                                'kitchen' => 'warning',
                                                                'cashier' => 'dark',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <div class="bg-{{ $bgColor }} rounded text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            {{ substr($user->name, 0, 2) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-modern bg-{{ $bgColor }}">
                                                    {{ match($user->role) {
                                                        'admin' => 'Admin',
                                                        'business_owner' => 'İşletme Sahibi',
                                                        'restaurant_manager' => 'Restoran Müdürü',
                                                        'waiter' => 'Garson',
                                                        'kitchen' => 'Mutfak',
                                                        'cashier' => 'Kasiyer',
                                                        default => 'Diğer'
                                                    } }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $user->created_at->format('d.m.Y') }}</div>
                                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
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
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="text-muted">Henüz kullanıcı yok</h5>
                            <p class="text-muted">Son kullanıcı kaydı bulunmamaktadır.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Businesses -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-trophy me-2"></i>
                        En Aktif İşletmeler
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($topBusinesses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Sıra</th>
                                        <th>İşletme</th>
                                        <th>Sahibi</th>
                                        <th>Restoran Sayısı</th>
                                        <th>Plan</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topBusinesses as $index => $business)
                                        <tr>
                                            <td>
                                                <div class="text-center">
                                                    @if($index < 3)
                                                        <span class="badge badge-modern bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-modern bg-light text-dark">{{ $index + 1 }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($business->logo)
                                                            <img src="{{ Storage::url($business->logo) }}" 
                                                                 class="rounded" width="40" height="40" style="object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="bi bi-building"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $business->name }}</div>
                                                        <small class="text-muted">{{ $business->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $business->owner->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $business->owner->email ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-primary">{{ $business->restaurants_count }}</div>
                                                    <small class="text-muted">Restoran</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-modern bg-{{ $business->plan == 'free' ? 'secondary' : 'success' }}">
                                                    {{ ucfirst($business->plan) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($business->is_active)
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
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 3rem;">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <h5 class="text-muted">Henüz işletme verisi yok</h5>
                            <p class="text-muted">Aktif işletme bulunmamaktadır.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 