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
                        <div class="mb-4">
                            <label for="logo" class="form-label">Restoran Logosu</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            @if($restaurant->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($restaurant->logo) }}" width="100" height="100" style="object-fit: cover; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="instagram" class="form-label">Instagram</label>
                                <input type="text" class="form-control" id="instagram" name="instagram" value="{{ old('instagram', $restaurant->instagram) }}" placeholder="Instagram URL">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $restaurant->whatsapp) }}" placeholder="WhatsApp URL">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $restaurant->twitter) }}" placeholder="Twitter URL">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facebook" class="form-label">Facebook</label>
                                <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $restaurant->facebook) }}" placeholder="Facebook URL">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="color_primary" class="form-label">1. Renk (Üst)</label>
                                <input type="color" class="form-control form-control-color" id="color_primary" name="color_primary" value="{{ old('color_primary', $restaurant->color_primary ?? '#FFD600') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="color_secondary" class="form-label">2. Renk (Alt)</label>
                                <input type="color" class="form-control form-control-color" id="color_secondary" name="color_secondary" value="{{ old('color_secondary', $restaurant->color_secondary ?? '#000000') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="color_cart" class="form-label">Sepet Rengi</label>
                                <input type="color" class="form-control form-control-color" id="color_cart" name="color_cart" value="{{ old('color_cart', $restaurant->color_cart ?? '#00C853') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="wifi_password" class="form-label">Wifi Şifresi</label>
                            <input type="text" class="form-control" id="wifi_password" name="wifi_password" value="{{ old('wifi_password', $restaurant->wifi_password) }}" placeholder="Wifi Şifresi">
                        </div>
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