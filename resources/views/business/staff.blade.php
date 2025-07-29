@extends('layouts.business')

@section('title', 'Çalışan Yönetimi - QR Menu')
@section('page-title', 'Çalışan Yönetimi')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">Personel Yönetimi</h3>
            <p class="text-muted mb-0">Restoranlarınızdaki tüm çalışanları buradan yönetebilirsiniz.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-business-modern" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-person-plus"></i>
                Yeni Personel Ekle
            </button>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-value">{{ $staff->count() }}</div>
                <div class="stats-label">Toplam Personel</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Tüm restoranlar
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stats-value">{{ $staff->where('is_active', true)->count() }}</div>
                <div class="stats-label">Aktif Personel</div>
                <div class="stats-change positive">
                    <i class="bi bi-check"></i>
                    Çalışan
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="stats-value">{{ $staff->where('role', 'waiter')->count() }}</div>
                <div class="stats-label">Garson</div>
                <div class="stats-change neutral">
                    <i class="bi bi-dash"></i>
                    Servis ekibi
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-fire"></i>
                </div>
                <div class="stats-value">{{ $staff->where('role', 'kitchen')->count() }}</div>
                <div class="stats-label">Mutfak</div>
                <div class="stats-change neutral">
                    <i class="bi bi-dash"></i>
                    Mutfak ekibi
                </div>
            </div>
        </div>
    </div>

    <!-- Personel Listesi -->
    <div class="content-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="bi bi-people me-2"></i>
                    Tüm Personel
                </h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="filterRole" style="width: 150px;">
                        <option value="">Tüm Roller</option>
                        <option value="restaurant_manager">Müdür</option>
                        <option value="waiter">Garson</option>
                        <option value="kitchen">Mutfak</option>
                        <option value="cashier">Kasiyer</option>
                    </select>
                    <select class="form-select form-select-sm" id="filterRestaurant" style="width: 200px;">
                        <option value="">Tüm Restoranlar</option>
                        @foreach($business->restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($staff->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern" id="staffTable">
                        <thead>
                            <tr>
                                <th>Personel</th>
                                <th>Restoran</th>
                                <th>Rol</th>
                                <th>Durum</th>
                                <th>Eklenme</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                                <tr data-role="{{ $member->role }}" data-restaurant="{{ $member->restaurant_id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $bgColor = match($member->role) {
                                                        'restaurant_manager' => 'danger',
                                                        'waiter' => 'primary',
                                                        'kitchen' => 'warning',
                                                        'cashier' => 'success',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <div class="bg-{{ $bgColor }} rounded text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($member->user->name, 0, 2) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $member->user->name }}</div>
                                                <small class="text-muted">{{ $member->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $member->restaurant->name }}</div>
                                        <small class="text-muted">{{ $member->restaurant->slug }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-modern bg-{{ $bgColor }}">
                                            {{ match($member->role) {
                                                'restaurant_manager' => 'Müdür',
                                                'waiter' => 'Garson',
                                                'kitchen' => 'Mutfak',
                                                'cashier' => 'Kasiyer',
                                                default => 'Diğer'
                                            } }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($member->is_active)
                                            <span class="badge badge-modern bg-success">Aktif</span>
                                        @else
                                            <span class="badge badge-modern bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $member->created_at->format('d.m.Y') }}</div>
                                        <small class="text-muted">{{ $member->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" 
                                                    onclick="editStaff({{ $member->id }})" 
                                                    data-bs-toggle="tooltip" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-{{ $member->is_active ? 'warning' : 'success' }}" 
                                                    onclick="toggleStaffStatus({{ $member->id }})"
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $member->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                <i class="bi bi-{{ $member->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="deleteStaff({{ $member->id }})" 
                                                    data-bs-toggle="tooltip" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($staff->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $staff->firstItem() }}-{{ $staff->lastItem() }} 
                                of {{ $staff->total() }} kayıt gösteriliyor
                            </small>
                            {{ $staff->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3" style="font-size: 3rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5 class="text-muted">Henüz personel eklenmemiş</h5>
                    <p class="text-muted">İlk personeli ekleyerek başlayın.</p>
                    <button class="btn btn-business-modern" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i class="bi bi-person-plus"></i>
                        İlk Personeli Ekle
                    </button>
                </div>
            @endif
        </div>
    </div>
@endsection

<!-- Personel Ekleme Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>
                    Yeni Personel Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStaffForm" action="{{ route('business.staff.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="restaurant_id" class="form-label">Restoran *</label>
                        <select class="form-select" id="restaurant_id" name="restaurant_id" required>
                            <option value="">Restoran seçin</option>
                            @foreach($business->restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user_email" class="form-label">Kullanıcı E-posta *</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" 
                               placeholder="personel@email.com" required>
                        <div class="form-text">Eğer kullanıcı sistemde yoksa otomatik oluşturulacak</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Personel Adı *</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" 
                               placeholder="Ad Soyad" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Rol seçin</option>
                            <option value="restaurant_manager">Restoran Müdürü</option>
                            <option value="waiter">Garson</option>
                            <option value="kitchen">Mutfak Personeli</option>
                            <option value="cashier">Kasiyer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="En az 6 karakter" minlength="6" required>
                        <div class="form-text">Personelin giriş yapacağı şifre</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-business-modern">
                        <i class="bi bi-check-circle"></i>
                        Personeli Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Filtreleme
    document.getElementById('filterRole').addEventListener('change', function() {
        filterTable();
    });
    
    document.getElementById('filterRestaurant').addEventListener('change', function() {
        filterTable();
    });
    
    function filterTable() {
        const roleFilter = document.getElementById('filterRole').value;
        const restaurantFilter = document.getElementById('filterRestaurant').value;
        const rows = document.querySelectorAll('#staffTable tbody tr');
        
        rows.forEach(row => {
            const role = row.dataset.role;
            const restaurant = row.dataset.restaurant;
            
            let showRow = true;
            
            if (roleFilter && role !== roleFilter) {
                showRow = false;
            }
            
            if (restaurantFilter && restaurant !== restaurantFilter) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Personel durumunu değiştir
    function toggleStaffStatus(staffId) {
        if (confirm('Personel durumunu değiştirmek istediğinizden emin misiniz?')) {
            fetch(`{{ route("business.staff.toggle-status", ":id") }}`.replace(':id', staffId), {
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
                alert('İşlem sırasında hata oluştu!');
            });
        }
    }
    
    // Personel sil
    function deleteStaff(staffId) {
        if (confirm('Bu personeli silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
            fetch(`{{ route("business.staff.delete", ":id") }}`.replace(':id', staffId), {
                method: 'DELETE',
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
                alert('İşlem sırasında hata oluştu!');
            });
        }
    }
    
    // Personel düzenle (basit implementation - modal'ı doldur)
    function editStaff(staffId) {
        // Bu fonksiyon daha sonra geliştirilebilir
        alert('Düzenleme özelliği yakında eklenecek!');
    }
    
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endsection 