@extends('layouts.business')

@section('title', 'Yeni Restoran Oluştur - QR Menu')
@section('page-title', 'Yeni Restoran Oluştur')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-shop me-2"></i>
                        Restoran Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('business.restaurants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Restoran Adı *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Adres</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="table_count" class="form-label">Masa Sayısı *</label>
                                <input type="number" class="form-control @error('table_count') is-invalid @enderror" 
                                       id="table_count" name="table_count" value="{{ old('table_count', 10) }}" 
                                       min="1" max="100" required>
                                @error('table_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="restaurant_manager_id" class="form-label">Restoran Müdürü</label>
                                <select class="form-control @error('restaurant_manager_id') is-invalid @enderror" 
                                        id="restaurant_manager_id" name="restaurant_manager_id">
                                    <option value="">Müdür Seç (Opsiyonel)</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('restaurant_manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('restaurant_manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="logo" class="form-label">Restoran Logosu</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, GIF formatlarında maksimum 2MB</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('business.restaurants') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Geri Dön
                            </a>
                            <button type="submit" class="btn btn-business-modern">
                                <i class="bi bi-check-circle"></i>
                                Restoranı Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection