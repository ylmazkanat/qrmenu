@extends('layouts.admin')

@section('title', 'İşletmeler - Admin Panel')
@section('page-title', 'İşletme Yönetimi')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">İşletmeler</li>
@endsection

@section('content')
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
                    Tüm işletmeler
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-value">{{ $stats['active_businesses'] }}</div>
                <div class="stats-label">Aktif İşletme</div>
                <div class="stats-change positive">
                    <i class="bi bi-graph-up"></i>
                    Çalışır durumda
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-gift"></i>
                </div>
                <div class="stats-value">{{ $stats['free_plan'] }}</div>
                <div class="stats-label">Ücretsiz Plan</div>
                <div class="stats-change neutral">
                    <i class="bi bi-dash"></i>
                    Free kullanıcılar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-star"></i>
                </div>
                <div class="stats-value">{{ $stats['paid_plans'] }}</div>
                <div class="stats-label">Ücretli Planlar</div>
                <div class="stats-change positive">
                    <i class="bi bi-currency-dollar"></i>
                    Premium kullanıcılar
                </div>
            </div>
        </div>
    </div>

    <!-- İşletmeler Listesi -->
    <div class="content-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="bi bi-building me-2"></i>
                    Tüm İşletmeler
                </h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" 
                           placeholder="İşletme ara..." style="width: 200px;">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($businesses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>İşletme</th>
                                <th>Sahibi</th>
                                <th>Plan</th>
                                <th>Restoranlar</th>
                                <th>Durum</th>
                                <th>Oluşturma</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($businesses as $business)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($business->logo)
                                                    <img src="{{ Storage::url($business->logo) }}" 
                                                         class="rounded" width="50" height="50" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $business->name }}</div>
                                                <small class="text-muted">{{ $business->slug }}</small>
                                                @if($business->description)
                                                    <div class="text-muted small mt-1">
                                                        {{ Str::limit($business->description, 50) }}
                                                    </div>
                                                @endif
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
                                        <span class="badge badge-modern bg-{{ $business->plan == 'free' ? 'secondary' : 'success' }}">
                                            {{ match($business->plan) {
                                                'free' => 'Ücretsiz',
                                                'basic' => 'Temel',
                                                'premium' => 'Premium',
                                                'enterprise' => 'Kurumsal',
                                                default => 'Bilinmiyor'
                                            } }}
                                        </span>
                                        @if($business->plan_expires_at)
                                            <div class="small text-muted">
                                                {{ $business->plan_expires_at->format('d.m.Y') }}'e kadar
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="fw-bold text-primary">{{ $business->restaurants_count }}</div>
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
                                    <td>
                                        <div>
                                            <div>{{ $business->created_at->format('d.m.Y') }}</div>
                                            <small class="text-muted">{{ $business->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.businesses.show', $business) }}" 
                                               class="btn btn-outline-info" 
                                               data-bs-toggle="tooltip" title="Detayları Gör">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-{{ $business->is_active ? 'warning' : 'success' }}" 
                                                    onclick="toggleBusinessStatus({{ $business->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $business->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="bi bi-{{ $business->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($businesses->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $businesses->firstItem() }}-{{ $businesses->lastItem() }} 
                                of {{ $businesses->total() }} kayıt gösteriliyor
                            </small>
                            {{ $businesses->links() }}
                        </div>
                    </div>
                @endif
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
@endsection

@section('scripts')
<script>
    function toggleBusinessStatus(businessId) {
        if (confirm('İşletme durumunu değiştirmek istediğinizden emin misiniz?')) {
            fetch(`/admin/businesses/${businessId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        }
    }

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endsection 