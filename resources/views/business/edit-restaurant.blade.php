@extends('layouts.business')

@section('title', 'Restoran Düzenle - QR Menu')
@section('page-title', 'Restoran Düzenle')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-pencil-square me-2"></i>
                        Restoran Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('business.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Restoran Adı *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $restaurant->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $restaurant->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Adres</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $restaurant->address) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="table_count" class="form-label">Masa Sayısı *</label>
                                <input type="number" class="form-control" id="table_count" name="table_count" value="{{ old('table_count', $restaurant->table_count) }}" min="1" max="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="restaurant_manager_id" class="form-label">Restoran Müdürü</label>
                                <select class="form-control" id="restaurant_manager_id" name="restaurant_manager_id">
                                    <option value="">Müdür Seç (Opsiyonel)</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ $restaurant->restaurant_manager_id == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="logo" class="form-label">Restoran Logosu</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                @if($restaurant->logo)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($restaurant->logo) }}" width="100" height="100" style="object-fit: cover; border-radius: 8px;">
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLogo()">Logoyu Kaldır</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="working_hours_text" class="form-label">Çalışma Saatleri</label>
                                <textarea class="form-control" id="working_hours_text" name="working_hours_text" rows="4" placeholder="Örnek: Pazartesi - Cuma: 09:00 - 22:00&#10;Cumartesi - Pazar: 10:00 - 23:00">{{ old('working_hours_text', $restaurant->working_hours_text) }}</textarea>
                                <small class="form-text text-muted">Her satıra bir gün yazabilirsiniz.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="primary_color" class="form-label">Ana Renk</label>
                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ old('primary_color', $restaurant->primary_color ?? '#6366f1') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="secondary_color" class="form-label">İkincil Renk</label>
                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $restaurant->secondary_color ?? '#8b5cf6') }}">
                            </div>
                        </div>

                        <!-- İletişim Bilgileri -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-telephone me-2"></i>
                                    İletişim Bilgileri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-posta</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $restaurant->email) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $restaurant->website) }}" placeholder="https://example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sosyal Medya -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-share me-2"></i>
                                    Sosyal Medya Linkleri
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook" class="form-label">
                                            <i class="bi bi-facebook text-primary me-1"></i>
                                            Facebook
                                        </label>
                                        <input type="url" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $restaurant->facebook) }}" placeholder="https://facebook.com/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="instagram" class="form-label">
                                            <i class="bi bi-instagram text-danger me-1"></i>
                                            Instagram
                                        </label>
                                        <input type="url" class="form-control" id="instagram" name="instagram" value="{{ old('instagram', $restaurant->instagram) }}" placeholder="https://instagram.com/username">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="twitter" class="form-label">
                                            <i class="bi bi-twitter text-info me-1"></i>
                                            Twitter
                                        </label>
                                        <input type="url" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $restaurant->twitter) }}" placeholder="https://twitter.com/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="youtube" class="form-label">
                                            <i class="bi bi-youtube text-danger me-1"></i>
                                            YouTube
                                        </label>
                                        <input type="url" class="form-control" id="youtube" name="youtube" value="{{ old('youtube', $restaurant->youtube) }}" placeholder="https://youtube.com/channel">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="linkedin" class="form-label">
                                            <i class="bi bi-linkedin text-primary me-1"></i>
                                            LinkedIn
                                        </label>
                                        <input type="url" class="form-control" id="linkedin" name="linkedin" value="{{ old('linkedin', $restaurant->linkedin) }}" placeholder="https://linkedin.com/company">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="whatsapp" class="form-label">
                                            <i class="bi bi-whatsapp text-success me-1"></i>
                                            WhatsApp
                                        </label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $restaurant->whatsapp) }}" placeholder="+90 555 123 4567">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Hidden inputs for remove actions -->
                        <input type="hidden" id="remove_logo" name="remove_logo" value="0">
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('business.restaurants.show', $restaurant->id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-business-modern">
                                <i class="bi bi-check-circle"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
function removeLogo() {
    if (confirm('Logoyu kaldırmak istediğinizden emin misiniz?')) {
        document.getElementById('remove_logo').value = '1';
        // Logo preview'ını gizle
        const logoPreview = document.querySelector('#logo').parentElement.querySelector('.mt-2');
        if (logoPreview) {
            logoPreview.style.display = 'none';
        }
    }
}


</script>