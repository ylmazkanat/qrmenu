@extends('layouts.admin')

@section('title', 'Restoranları Yönet - QR Menu Admin')
@section('page-title', 'Restoranları Yönet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Restoranlar</li>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">Restoranları Yönet</h3>
            <p class="text-muted mb-0">Sistemdeki tüm restoranları görüntüleyin ve yönetin</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stats-value">{{ $restaurants->total() }}</div>
                <div class="stats-label">Toplam Restoran</div>
                <div class="stats-change positive">
                    <i class="bi bi-plus-circle"></i>
                    Tüm restoranlar
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-value">{{ \App\Models\Restaurant::where('is_active', true)->count() }}</div>
                <div class="stats-label">Aktif Restoran</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    {{ round((\App\Models\Restaurant::where('is_active', true)->count() / max($restaurants->total(), 1)) * 100, 1) }}% aktif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-pause-circle"></i>
                </div>
                <div class="stats-value">{{ \App\Models\Restaurant::where('is_active', false)->count() }}</div>
                <div class="stats-label">Pasif Restoran</div>
                <div class="stats-change negative">
                    <i class="bi bi-arrow-down"></i>
                    Beklemede
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-calendar-day"></i>
                </div>
                <div class="stats-value">{{ \App\Models\Restaurant::whereDate('created_at', today())->count() }}</div>
                <div class="stats-label">Bugün Eklenen</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-check"></i>
                    Yeni kayıtlar
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Table -->
    <div class="content-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title">
                        <i class="bi bi-list-ul me-2"></i>
                        Restoran Listesi
                    </h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">{{ $restaurants->total() }} restoran bulundu</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($restaurants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Restoran</th>
                                <th>Sahibi</th>
                                <th>İstatistikler</th>
                                <th>Durum</th>
                                <th>Oluşturma</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $restaurant)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($restaurant->logo)
                                                    <img src="{{ Storage::url($restaurant->logo) }}" 
                                                         class="rounded" width="48" height="48" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 48px; height: 48px;">
                                                        <i class="bi bi-shop"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $restaurant->name }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-link-45deg"></i> 
                                                    <code>{{ $restaurant->slug }}</code>
                                                </small>
                                                @if($restaurant->description)
                                                    <div class="text-muted small mt-1">
                                                        {{ Str::limit($restaurant->description, 60) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $owner = $restaurant->business?->owner;
                                        @endphp
                                        <div>
                                            <div class="fw-medium">{{ $owner?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $owner?->email ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row g-3">
                                            <div class="col-4 text-center">
                                                <div class="text-primary fw-bold">{{ $restaurant->categories->count() }}</div>
                                                <small class="text-muted">Kategori</small>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="text-success fw-bold">{{ $restaurant->products->count() }}</div>
                                                <small class="text-muted">Ürün</small>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="text-warning fw-bold">{{ $restaurant->orders->count() }}</div>
                                                <small class="text-muted">Sipariş</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($restaurant->is_active)
                                            <span class="badge badge-modern bg-success">
                                                <i class="bi bi-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-modern bg-danger">
                                                <i class="bi bi-x-circle"></i> Pasif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $restaurant->created_at->format('d.m.Y') }}</div>
                                            <small class="text-muted">{{ $restaurant->created_at->format('H:i') }}</small>
                                            <div class="text-muted small mt-1">
                                                {{ $restaurant->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('menu.show', $restaurant->slug) }}" 
                                               class="btn btn-outline-primary" target="_blank" 
                                               data-bs-toggle="tooltip" title="Menüyü Görüntüle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" 
                                                    data-bs-toggle="tooltip" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-info" 
                                                    data-bs-toggle="tooltip" title="İstatistikler">
                                                <i class="bi bi-graph-up"></i>
                                            </button>
                                            <button class="btn btn-outline-{{ $restaurant->is_active ? 'warning' : 'success' }}" 
                                                    data-bs-toggle="tooltip" title="{{ $restaurant->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="bi bi-{{ $restaurant->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($restaurants->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    {{ $restaurants->firstItem() }} - {{ $restaurants->lastItem() }} 
                                    arası, toplam {{ $restaurants->total() }} kayıt
                                </small>
                            </div>
                            <div>
                                {{ $restaurants->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3" style="font-size: 4rem;">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h4 class="text-muted mb-2">Henüz restoran bulunmuyor</h4>
                    <p class="text-muted">Sistem kullanıcıları henüz restoran oluşturmamış.</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-primary-modern mt-3">
                        <i class="bi bi-people"></i>
                        Kullanıcıları Gör
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush 