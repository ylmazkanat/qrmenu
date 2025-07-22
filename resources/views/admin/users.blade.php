@extends('layouts.admin')

@section('title', 'Kullanıcıları Yönet - QR Menu Admin')
@section('page-title', 'Kullanıcıları Yönet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Kullanıcılar</li>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">Kullanıcıları Yönet</h3>
            <p class="text-muted mb-0">Sistemdeki tüm kullanıcıları görüntüleyin ve yönetin</p>
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
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-value">{{ $users->total() }}</div>
                <div class="stats-label">Toplam Kullanıcı</div>
                <div class="stats-change positive">
                    <i class="bi bi-plus-circle"></i>
                    Tüm kullanıcılar
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stats-value">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
                <div class="stats-label">Admin</div>
                <div class="stats-change positive">
                    <i class="bi bi-shield"></i>
                    Yöneticiler
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stats-value">{{ \App\Models\User::where('role', 'restaurant_owner')->count() }}</div>
                <div class="stats-label">Restoran Sahibi</div>
                <div class="stats-change positive">
                    <i class="bi bi-briefcase"></i>
                    İşletmeciler
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-calendar-day"></i>
                </div>
                <div class="stats-value">{{ \App\Models\User::whereDate('created_at', today())->count() }}</div>
                <div class="stats-label">Bugün Katılan</div>
                <div class="stats-change positive">
                    <i class="bi bi-person-plus"></i>
                    Yeni üyeler
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="content-card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Kullanıcı Listesi
                    </h5>
                </div>
                <div class="col-md-8 text-md-end">
                    <form class="row g-2 justify-content-md-end" method="GET" action="{{ route('admin.users') }}">
                        <div class="col-auto">
                            <input type="text" name="search" class="form-control" placeholder="Ara..." value="{{ request('search') }}">
                        </div>
                        <div class="col-auto">
                            <select name="role" class="form-select">
                                <option value="">Tüm Roller</option>
                                @foreach(['admin','restaurant_owner','business_owner','restaurant_manager','waiter','kitchen','cashier'] as $role)
                                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $role)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary-modern" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                    <small class="text-muted d-block mt-2 mt-md-0">{{ $users->total() }} kullanıcı bulundu</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Rol</th>
                                <th>İlişkili İşletme/Restoran</th>
                                <th>Hesap Durumu</th>
                                <th>Kayıt Tarihi</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $bgColor = match($user->role) {
                                                        'admin' => 'danger',
                                                        'restaurant_owner' => 'success',
                                                        'waiter' => 'info',
                                                        'kitchen' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <div class="bg-{{ $bgColor }} rounded text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 48px; height: 48px;">
                                                    @if($user->role == 'admin')
                                                        <i class="bi bi-shield-check"></i>
                                                    @elseif($user->role == 'restaurant_owner')
                                                        <i class="bi bi-shop"></i>
                                                    @elseif($user->role == 'waiter')
                                                        <i class="bi bi-person-badge"></i>
                                                    @elseif($user->role == 'kitchen')
                                                        <i class="bi bi-tools"></i>
                                                    @else
                                                        <i class="bi bi-person"></i>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock"></i>
                                                        {{ floor($user->created_at->diffInDays()) }} gün önce
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->role == 'admin')
                                            <span class="badge badge-modern bg-danger">
                                                <i class="bi bi-shield-check"></i> Admin
                                            </span>
                                        @elseif($user->role == 'restaurant_owner')
                                            <span class="badge badge-modern bg-success">
                                                <i class="bi bi-shop"></i> Restoran Sahibi
                                            </span>
                                        @elseif($user->role == 'waiter')
                                            <span class="badge badge-modern bg-info">
                                                <i class="bi bi-person-badge"></i> Garson
                                            </span>
                                        @elseif($user->role == 'kitchen')
                                            <span class="badge badge-modern bg-warning">
                                                <i class="bi bi-tools"></i> Mutfak
                                            </span>
                                        @else
                                            <span class="badge badge-modern bg-secondary">
                                                <i class="bi bi-person"></i> Diğer
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // İlşkili restoran listesi
                                            $relatedRestaurants = collect();
                                            if ($user->role == 'restaurant_owner') {
                                                if (isset($user->restaurants)) {
                                                    $relatedRestaurants = $user->restaurants;
                                                } else {
                                                    $relatedRestaurants = $user->ownedBusinesses->flatMap(fn($biz) => $biz->restaurants);
                                                }
                                            } elseif ($user->role == 'restaurant_manager') {
                                                $relatedRestaurants = $user->managedRestaurants;
                                            } else {
                                                $relatedRestaurants = $user->restaurantStaff->map(fn($staff) => $staff->restaurant);
                                            }
                                        @endphp

                                        @if($user->role == 'business_owner' && $user->ownedBusinesses->count() > 0)
                                            <div class="text-center">
                                                <div class="text-success fw-bold fs-5">{{ $user->ownedBusinesses->count() }}</div>
                                                <small class="text-muted">İşletme</small>
                                                <div class="mt-1">
                                                    <small class="text-info">
                                                        <i class="bi bi-building"></i>
                                                        {{ $user->ownedBusinesses->where('is_active', true)->count() }} aktif
                                                    </small>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">{{ $user->ownedBusinesses->pluck('name')->take(2)->join(', ') }}{{ $user->ownedBusinesses->count() > 2 ? '...' : '' }}</small>
                                                </div>
                                            </div>
                                        @elseif($relatedRestaurants->count() > 0)
                                            <div class="text-center">
                                                <div class="text-primary fw-bold fs-5">{{ $relatedRestaurants->count() }}</div>
                                                <small class="text-muted">Restoran</small>
                                                <div class="mt-1">
                                                    <small class="text-success">
                                                        <i class="bi bi-check-circle"></i>
                                                        {{ $relatedRestaurants->where('is_active', true)->count() }} aktif
                                                    </small>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">{{ $relatedRestaurants->pluck('name')->take(2)->join(', ') }}{{ $relatedRestaurants->count() > 2 ? '...' : '' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted text-center d-block">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if($user->email_verified_at)
                                                <span class="badge badge-modern bg-success">
                                                    <i class="bi bi-check-circle"></i> Doğrulanmış
                                                </span>
                                            @else
                                                <span class="badge badge-modern bg-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> Doğrulanmamış
                                                </span>
                                            @endif
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    Son giriş: {{ $user->updated_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $user->created_at->format('d.m.Y') }}</div>
                                            <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                            <div class="mt-1">
                                                <small class="text-primary">
                                                    <i class="bi bi-calendar"></i>
                                                    {{ floor($user->created_at->diffInDays()) }} gün önce
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            @if($user->role == 'restaurant_owner' && $user->restaurants->count() > 0)
                                                <a href="{{ route('menu.show', $user->restaurants->first()->slug) }}" 
                                                   class="btn btn-outline-primary" target="_blank" 
                                                   data-bs-toggle="tooltip" title="Restoran Menüsü">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                            <button class="btn btn-outline-secondary" 
                                                    data-bs-toggle="tooltip" title="Profil Detayları">
                                                <i class="bi bi-person"></i>
                                            </button>
                                            <button class="btn btn-outline-info" 
                                                    data-bs-toggle="tooltip" title="Mesaj Gönder">
                                                <i class="bi bi-envelope"></i>
                                            </button>
                                            <a href="{{ route('admin.users.impersonate', $user) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-success" 
                                               data-bs-toggle="tooltip" 
                                               title="Hesaba Giriş Yap">
                                                <i class="bi bi-box-arrow-in-right"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button class="btn btn-outline-warning" 
                                                        data-bs-toggle="tooltip" title="Hesabı Dondur">
                                                    <i class="bi bi-ban"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    {{ $users->firstItem() }} - {{ $users->lastItem() }} 
                                    arası, toplam {{ $users->total() }} kayıt
                                </small>
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3" style="font-size: 4rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="text-muted mb-2">Henüz kullanıcı bulunmuyor</h4>
                    <p class="text-muted">Sisteme henüz kullanıcı kaydolmamış.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
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
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-primary-modern w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-person-plus fs-1 mb-2"></i>
                                    <div class="fw-medium">Yeni Kullanıcı Ekle</div>
                                    <small class="text-muted">Manuel kullanıcı oluştur</small>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-outline-info w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-envelope fs-1 mb-2"></i>
                                    <div class="fw-medium">Toplu E-posta</div>
                                    <small class="text-muted">Kullanıcılara bildirim gönder</small>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-outline-success w-100 p-3">
                                <div class="text-center">
                                    <i class="bi bi-download fs-1 mb-2"></i>
                                    <div class="fw-medium">Rapor İndir</div>
                                    <small class="text-muted">Kullanıcı listesi ve istatistikler</small>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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