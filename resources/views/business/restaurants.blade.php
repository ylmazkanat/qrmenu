@extends('layouts.business')

@section('title', 'Restoranlarım - QR Menu')
@section('page-title', 'Restoranlarım')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-2">Restoranlarınızı Yönetin</h3>
            <p class="text-muted mb-0">İşletmenize ait tüm restoranları buradan kontrol edebilirsiniz.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('business.restaurants.create') }}" class="btn btn-business-modern">
                <i class="bi bi-plus-circle"></i>
                Yeni Restoran Ekle
            </a>
        </div>
    </div>

    @if($restaurants->count() > 0)
        <div class="row">
            @foreach($restaurants as $restaurant)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="content-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    @if($restaurant->logo)
                                        <img src="{{ Storage::url($restaurant->logo) }}" 
                                             class="rounded" width="60" height="60" style="object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="bi bi-shop fs-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $restaurant->name }}</h5>
                                    <small class="text-muted">{{ $restaurant->slug }}</small>
                                    <div class="mt-1">
                                        @if($restaurant->is_active)
                                            <span class="badge badge-modern bg-success">Aktif</span>
                                        @else
                                            <span class="badge badge-modern bg-danger">Pasif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($restaurant->description)
                                <p class="text-muted small mb-3">{{ Str::limit($restaurant->description, 100) }}</p>
                            @endif

                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="text-primary fw-bold">{{ $restaurant->categories->count() }}</div>
                                    <small class="text-muted">Kategori</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-success fw-bold">{{ $restaurant->products->count() }}</div>
                                    <small class="text-muted">Ürün</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-warning fw-bold">{{ $restaurant->orders->count() }}</div>
                                    <small class="text-muted">Sipariş</small>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('menu.show', $restaurant->slug) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill" target="_blank">
                                    <i class="bi bi-eye"></i>
                                    Menüyü Gör
                                </a>
                                <a href="{{ route('business.restaurants.show', $restaurant) }}" 
                                   class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="bi bi-gear"></i>
                                    Yönet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($restaurants->hasPages())
            <div class="d-flex justify-content-center">
                {{ $restaurants->links() }}
            </div>
        @endif
    @else
        <div class="content-card">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-4" style="font-size: 4rem;">
                    <i class="bi bi-shop"></i>
                </div>
                <h4 class="text-muted mb-3">Henüz restoran eklenmemiş</h4>
                <p class="text-muted mb-4">İlk restoranınızı oluşturun ve QR menü sistemini kullanmaya başlayın.</p>
                <a href="{{ route('business.restaurants.create') }}" class="btn btn-business-modern">
                    <i class="bi bi-plus-circle"></i>
                    İlk Restoranı Oluştur
                </a>
            </div>
        </div>
    @endif
@endsection 