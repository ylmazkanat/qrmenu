@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yeni Paket Ekle</h1>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.packages.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- Temel Bilgiler -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Temel Bilgiler</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Paket Adı *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Fiyat (₺) *</label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="billing_cycle">Ödeme Döngüsü *</label>
                                    <select class="form-control @error('billing_cycle') is-invalid @enderror" 
                                            id="billing_cycle" name="billing_cycle" required>
                                        <option value="">Seçiniz</option>
                                        <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                                        <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yıllık</option>
                                    </select>
                                    @error('billing_cycle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sort_order">Sıralama</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                               {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Aktif</label>
                                    </div>
                                    
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_popular" name="is_popular" 
                                               {{ old('is_popular') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_popular">Popüler</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sayısal Limitler -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Sayısal Limitler</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feature_max_restaurants">Maksimum Restoran Sayısı</label>
                                    <input type="number" class="form-control" id="feature_max_restaurants" 
                                           name="feature_max_restaurants" value="{{ old('feature_max_restaurants', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = Sınırsız</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feature_max_managers">Maksimum Müdür Hesabı</label>
                                    <input type="number" class="form-control" id="feature_max_managers" 
                                           name="feature_max_managers" value="{{ old('feature_max_managers', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = Sınırsız</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feature_max_staff">Maksimum Çalışan Sayısı</label>
                                    <input type="number" class="form-control" id="feature_max_staff" 
                                           name="feature_max_staff" value="{{ old('feature_max_staff', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = Sınırsız</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feature_max_products">Maksimum Ürün Sayısı</label>
                                    <input type="number" class="form-control" id="feature_max_products" 
                                           name="feature_max_products" value="{{ old('feature_max_products', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = Sınırsız</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="feature_max_categories">Maksimum Kategori Sayısı</label>
                                    <input type="number" class="form-control" id="feature_max_categories" 
                                           name="feature_max_categories" value="{{ old('feature_max_categories', 0) }}" min="0">
                                    <small class="form-text text-muted">0 = Sınırsız</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Özellikler -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Özellikler ve İzinler</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($predefinedFeatures as $key => $feature)
                                @if($feature['type'] == 'boolean')
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="feature_{{ $key }}" name="feature_{{ $key }}" 
                                                   {{ old("feature_{$key}") ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="feature_{{ $key }}">
                                                <strong>{{ $feature['name'] }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $feature['description'] }}</small>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Özet -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Paket Özeti</h6>
                    </div>
                    <div class="card-body">
                        <div id="package-summary">
                            <p class="text-muted">Paket bilgilerini girdikten sonra özet burada görünecek.</p>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Paketi Kaydet
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-block mt-2">
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function updateSummary() {
        const name = $('#name').val() || 'Paket Adı';
        const price = $('#price').val() || '0';
        const billingCycle = $('#billing_cycle option:selected').text() || 'Aylık';
        const isActive = $('#is_active').is(':checked');
        const isPopular = $('#is_popular').is(':checked');
        
        let features = [];
        $('input[type="checkbox"]:checked').each(function() {
            const label = $(this).next('label').find('strong').text();
            features.push(label);
        });
        
        let limits = [];
        $('input[type="number"]').each(function() {
            const value = $(this).val();
            const label = $(this).prev('label').text();
            if (value > 0) {
                limits.push(`${label}: ${value}`);
            }
        });
        
        let summary = `
            <h5>${name}</h5>
            <p><strong>Fiyat:</strong> ${price} ₺ / ${billingCycle}</p>
            <p><strong>Durum:</strong> ${isActive ? 'Aktif' : 'Pasif'}</p>
            ${isPopular ? '<p><strong>Popüler Paket</strong></p>' : ''}
        `;
        
        if (limits.length > 0) {
            summary += '<h6>Limitler:</h6><ul>';
            limits.forEach(limit => {
                summary += `<li>${limit}</li>`;
            });
            summary += '</ul>';
        }
        
        if (features.length > 0) {
            summary += '<h6>Özellikler:</h6><ul>';
            features.forEach(feature => {
                summary += `<li>${feature}</li>`;
            });
            summary += '</ul>';
        }
        
        $('#package-summary').html(summary);
    }
    
    // Form alanları değiştiğinde özeti güncelle
    $('input, select, textarea').on('input change', updateSummary);
    
    // Sayfa yüklendiğinde özeti göster
    updateSummary();
});
</script>
@endsection 