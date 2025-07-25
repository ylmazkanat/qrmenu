@extends('layouts.menu')

@section('content')
<div class="container">
    <!-- Categories & Products -->
    <div class="row">
        <div class="col-12 mb-4">
            @foreach($restaurant->categories as $category)
                @if($category->products->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h2 class="mb-0">{{ $category->name }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($category->products as $product)
                                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 product-card" data-product-id="{{ $product->id }}">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title d-flex justify-content-between">
                                                    <span>{{ $product->name }}</span>
                                                    <span class="text-primary">{{ number_format($product->price, 2) }} ₺</span>
                                                </h5>
                                                @if($product->description)
                                                    <p class="card-text text-muted">{{ $product->description }}</p>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white border-top-0">
                                                <button class="btn btn-primary btn-sm w-100 add-to-cart-btn" 
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-product-price="{{ $product->price }}">
                                                    <i class="bi bi-plus-lg me-1"></i>Sepete Ekle
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Cart Button -->
<button class="btn btn-primary cart-button" data-bs-toggle="modal" data-bs-target="#cartModal">
    <i class="bi bi-cart-fill fs-5"></i>
    <span class="cart-badge" id="cartBadge">0</span>
</button>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sepetim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cartItems">
                <!-- Cart items will be loaded here -->
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <strong>Toplam: <span id="cartTotal">0.00</span> ₺</strong>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <a href="{{ route('menu.cart', $restaurant->slug) }}" class="btn btn-primary">Sipariş Ver</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateCart();
        
        // Add to Cart Button Click
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productPrice = parseFloat(this.dataset.productPrice);
                
                addToCart(productId, productName, productPrice);
            });
        });
    });
    
    function addToCart(productId, productName, productPrice) {
        fetch('{{ route('menu.cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCart();
                showToast('Ürün sepete eklendi');
            }
        });
    }
    
    function updateCart() {
        fetch('{{ route('menu.cart', $restaurant->slug) }}')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cartBadge');
                const cartItems = document.getElementById('cartItems');
                const cartTotal = document.getElementById('cartTotal');
                let html = '';
                
                cartBadge.textContent = data.items.reduce((sum, item) => sum + item.quantity, 0);
                
                if (data.items.length === 0) {
                    html = '<p class="text-center">Sepetiniz boş</p>';
                } else {
                    data.items.forEach(item => {
                        html += `
                            <div class="cart-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${item.name}</strong><br>
                                        <small>${item.quantity} adet × ${item.price} ₺</small>
                                    </div>
                                    <div class="quantity-control">
                                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                        <span>${item.quantity}</span>
                                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                cartItems.innerHTML = html;
                cartTotal.textContent = data.total.toFixed(2);
            });
    }
    
    function updateQuantity(itemId, newQuantity) {
        if (newQuantity < 0) return;
        
        fetch('{{ route('menu.cart.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCart();
            }
        });
    }
    
    function showToast(message) {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div class="toast-body">
                ${message}
            </div>
        `;
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
</script>
@endpush
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-color);
        }

        .restaurant-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem 0;
            position: relative;
            overflow: hidden;
        }

        .restaurant-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="pattern" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23pattern)"/></svg>');
            opacity: 0.3;
        }

        .restaurant-header .container {
            position: relative;
            z-index: 2;
        }

        .search-section {
            background: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            padding: 12px 50px 12px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
        }

        .product-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
        }

        .category-card .card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.15);
        }

        #backToCategoriesBtn {
            border-radius: 12px;
            font-weight: 500;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        #backToCategoriesBtn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .product-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .product-image::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.3));
        }

        .product-price {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--success-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--warning-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .product-content {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .product-description {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-modern {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            padding: 8px 16px;
        }

        .btn-add-cart {
            background: var(--primary-color);
            color: white;
            flex: 1;
        }

        .btn-add-cart:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            color: white;
        }

        .btn-detail {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-detail:hover {
            background: #e2e8f0;
            color: #374151;
        }

        .cart-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 20px;
            box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .cart-fab:hover {
            transform: scale(1.1);
            background: #059669;
            color: white;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-modern .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-modern .modal-header {
            border: none;
            padding: 1.5rem 1.5rem 0;
        }

        .modal-modern .modal-body {
            padding: 1rem 1.5rem;
        }

        .modal-modern .modal-footer {
            border: none;
            padding: 0 1.5rem 1.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .search-results {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 1rem;
        }

        .search-result-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .search-result-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .restaurant-header {
                padding: 1.5rem 0;
            }
            
            .product-image {
                height: 160px;
            }
            
            .cart-fab {
                bottom: 15px;
                right: 15px;
                width: 55px;
                height: 55px;
                font-size: 18px;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Welcome Screen Styles */
        #welcomeScreen {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(180deg, #FFD600 0%, #000 100%);
            padding: 1rem;
        }

        #welcomeScreen img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        #welcomeScreen h2 {
            font-weight: 700;
            color: #FFD600;
            margin: 1rem 0;
        }

        #welcomeScreen .btn {
            max-width: 300px;
        }

        #welcomeScreen .btn-pink {
            background: linear-gradient(135deg, #D5006D, #FF4081);
            color: white;
        }

        #welcomeScreen .btn-success {
            background: #25D366;
            color: white;
        }

        #welcomeScreen .btn-dark {
            background: #343a40;
            color: white;
        }

        #welcomeScreen .form-select {
            border-radius: 12px;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <!-- Welcome Screen -->
    <div id="welcomeScreen" class="d-flex flex-column min-vh-100" style="background: linear-gradient(180deg, {{ $restaurant->color_primary ?? '#FFD600' }} 0%, {{ $restaurant->color_secondary ?? '#000' }} 100%);">
        <!-- Social Media Icons Sabit Üstte -->
        <div class="d-flex gap-2 justify-content-center py-3">
            @if($restaurant->instagram)
            <a href="{{ $restaurant->instagram }}" target="_blank" class="btn btn-sm btn-pink"><i class="bi bi-instagram"></i></a>
            @endif
            @if($restaurant->whatsapp)
            <a href="{{ $restaurant->whatsapp }}" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i></a>
            @endif
            @if($restaurant->twitter)
            <a href="{{ $restaurant->twitter }}" target="_blank" class="btn btn-sm btn-info"><i class="bi bi-twitter"></i></a>
            @endif
            @if($restaurant->facebook)
            <a href="{{ $restaurant->facebook }}" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-facebook"></i></a>
            @endif
        </div>
        <!-- Logo ve İçerik Ortalı -->
        <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center">
            <div class="mb-4">
                <img src="{{ $restaurant->logo ? Storage::url($restaurant->logo) : ($restaurant->logo_url ?? '/default-logo.png') }}" alt="Logo" class="rounded shadow" style="width: 120px; height: 120px; object-fit: cover;">
            </div>
            <h2 class="fw-bold text-warning mb-3">{{ $restaurant->name }}</h2>
            <div class="text-light mb-3 text-center px-3" style="max-width: 600px;">{{ $restaurant->description ?? '' }}</div>
        @if($restaurant->wifi_password)
        <div class="text-warning small mb-2">Wifi Şifresi: {{ $restaurant->wifi_password }}</div>
        @endif
        <!-- Language Selector -->
        @if(config('app.multilanguage'))
        <div class="mb-3" style="width: 220px;">
            <select id="languageSelect" class="form-select">
                <option value="tr">🇹🇷 Türkçe</option>
                <option value="en">🇬🇧 English</option>
                <!-- Diğer diller dinamik eklenebilir -->
            </select>
        </div>
        @endif
        <button id="welcomeMenuBtn" class="btn btn-dark w-75 mb-2" style="max-width: 300px; border: 2px solid #FFD600;">
            Menü
        </button>
        <button id="welcomeRateBtn" class="btn btn-outline-light w-75" style="max-width: 300px;">
            <i class="bi bi-chat-dots"></i> Bizi değerlendirin!
        </button>
    </div>

    <!-- Restaurant Header -->
    <div class="restaurant-header">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <h1 class="mb-0">
                        <i class="bi bi-shop me-2"></i>
                        {{ $restaurant->name }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="d-flex gap-2 gap-md-3 align-items-center">
                <!-- Kategorilere Git Butonu -->
                <button class="btn btn-outline-primary flex-shrink-0" id="backToCategoriesBtn" style="display: none;" onclick="goBackToCategories()">
                    <i class="bi bi-arrow-left me-2"></i>
                    <span class="d-none d-md-inline">Kategorilere Git</span>
                    <span class="d-md-none">Kategoriler</span>
                </button>
                
                <!-- Arama Kutusu -->
                <div class="search-box flex-grow-1">
                    <input type="text" class="search-input" id="searchInput" placeholder="Ürün ara... (örn: pizza, köfte, içecek)">
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
            
            <!-- Search Results -->
            <div id="searchResults" class="search-results" style="display: none;"></div>
        </div>
    </div>

    <!-- Category Grid -->
    @if($restaurant->categories->count() > 0)
        <div class="container py-5" id="categoryGrid">
            <div class="row">
                <!-- Tümü Seçeneği -->
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <a href="#all-products" class="text-decoration-none d-block category-card" data-target="all-products">
                        <div class="card h-100 shadow-sm border-0">
                            <div style="height: 160px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-grid-3x3-gap-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                            <div class="card-body text-center">
                                <h6 class="mb-0">Tümü</h6>
                            </div>
                        </div>
                    </a>
                </div>
                
                @foreach($restaurant->categories as $category)
                    @php 
                        $catSlug = $category->slug ?: \Illuminate\Support\Str::slug($category->name.'-'.$category->id); 
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <a href="#category-{{ $catSlug }}" class="text-decoration-none d-block category-card" data-target="category-{{ $catSlug }}">
                            <div class="card h-100 shadow-sm border-0">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" class="card-img-top" style="height: 160px; object-fit: contain; background: #f8f9fa;">
                                @else
                                    <div style="height: 160px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body text-center">
                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Menu Content -->
    <div class="container py-4" id="menuContent" style="display: none;">
        @if($restaurant->categories->count() > 0)
            @foreach($restaurant->categories as $category)
                @php $catSlug = $category->slug ?: \Illuminate\Support\Str::slug($category->name.'-'.$category->id); @endphp
                <div id="category-{{ $catSlug }}" class="category-section mb-5">
                    <h3 class="mb-4">
                        <i class="bi bi-bookmark-fill text-primary me-2"></i>
                        {{ $category->name }}
                    </h3>
                    
                    @if($category->products->count() > 0)
                        <div class="row">
                            @foreach($category->products as $product)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="product-card fade-in">
                                        <div class="product-image" 
                                             @if($product->image) 
                                                style="background: #f8f9fa url('{{ Storage::url($product->image) }}') center/contain no-repeat;"
                                             @else
                                                style="background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center;"
                                             @endif>
                                            
                                            @if(!$product->image)
                                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                            @endif
                                            
                                            <div class="product-price">{{ number_format($product->price, 2) }} ₺</div>
                                            
                                            @if(!$product->inStock())
                                                <div class="product-badge bg-danger">Tükendi</div>
                                            @endif
                                        </div>
                                        
                                        <div class="product-content">
                                            <h5 class="product-title">{{ $product->name }}</h5>
                                            
                                            @if($product->description)
                                                <p class="product-description">{{ $product->description }}</p>
                                            @endif
                                            
                                            <div class="product-actions">
                                                @php
                                                    $showAddToCart = false;
                                                    if($restaurant->orderSettings && $restaurant->orderSettings->ordering_enabled) {
                                                        $enabledCategories = $restaurant->orderSettings->enabled_categories ?? [];
                                                        if(in_array('all', $enabledCategories) || in_array($category->id, $enabledCategories)) {
                                                            $showAddToCart = true;
                                                        }
                                                    }
                                                @endphp
                                                
                                                @if($showAddToCart && $product->inStock())
                                                    <button class="btn btn-add-cart btn-modern add-to-cart" 
                                                            data-product-id="{{ $product->id }}"
                                                            data-product-name="{{ $product->name }}"
                                                            data-product-price="{{ $product->price }}"
                                                            data-product-image="{{ $product->image ? Storage::url($product->image) : '' }}">
                                                        <i class="bi bi-cart-plus me-2"></i>
                                                        Sepete Ekle
                                                    </button>
                                                @elseif($showAddToCart && !$product->inStock())
                                                    <button class="btn btn-secondary btn-modern" disabled>
                                                        <i class="bi bi-x-circle me-2"></i>
                                                        Tükendi
                                                    </button>
                                                @endif
                                                
                                                @if($product->description && strlen($product->description) > 50)
                                                    <button class="btn btn-detail btn-modern" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#productModal"
                                                            data-product-name="{{ $product->name }}"
                                                            data-product-description="{{ $product->description }}"
                                                            data-product-price="{{ $product->price }}"
                                                            data-product-image="{{ $product->image ? Storage::url($product->image) : '' }}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-box"></i>
                            </div>
                            <h5 class="text-muted">Bu kategoride ürün bulunmuyor</h5>
                            <p class="text-muted">Yakında yeni ürünler eklenecek.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-journal-text"></i>
                </div>
                <h4 class="text-muted">Menü Hazırlanıyor</h4>
                <p class="text-muted">Yakında lezzetli ürünlerimizi burada bulabileceksiniz.</p>
            </div>
        @endif
    </div>

    <!-- Cart FAB -->
    <button class="cart-fab" id="cartFab" style="display: block; background: {{ $restaurant->color_cart ?? '#00C853' }};" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="bi bi-cart"></i>
        <span class="cart-badge" id="cartBadge">0</span>
    </button>

    <!-- Product Detail Modal -->
    <div class="modal fade modal-modern" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Ürün Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="productModalImage" class="mb-3" style="height: 200px; border-radius: 12px; background-size: cover; background-position: center;"></div>
                    <p id="productModalDescription" class="text-muted"></p>
                    <h4 id="productModalPrice" class="text-success"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="addFromModal">
                        <i class="bi bi-cart-plus me-2"></i>
                        Sepete Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade modal-modern" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-cart me-2"></i>
                        Sepetim
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems"></div>
                    <div id="emptyCart" class="empty-state" style="display: none;">
                        <div class="empty-state-icon">
                            <i class="bi bi-cart"></i>
                        </div>
                        <h5 class="text-muted">Sepetiniz boş</h5>
                        <p class="text-muted">Lezzetli ürünlerimizi sepete ekleyerek başlayın.</p>
                    </div>
                    <hr class="my-4">
                    <div id="activeOrdersArea">
                        <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i> Masadaki Aktif Siparişlerim</h6>
                        <div id="activeOrdersList" class="mb-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Toplam:</h5>
                            <h4 class="mb-0 text-success" id="cartTotal">0.00 ₺</h4>
                        </div>
                        <button type="button" class="btn btn-success btn-lg w-100" id="placeOrder" disabled>
                            <i class="bi bi-check-circle me-2"></i>
                            Siparişi Tamamla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Number Modal -->
    <div class="modal fade modal-modern" id="tableModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-table me-2"></i>
                        Masa Numarası
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tableNumber" class="form-label">Masa numaranızı girin:</label>
                        <input type="text" class="form-control form-control-lg" id="tableNumber" placeholder="Örn: 5" style="border-radius: 12px;">
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Masa numaranız siparişinizin doğru masaya ulaşması için gereklidir.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="confirmTable">
                        <i class="bi bi-check-circle me-2"></i>
                        Devam Et
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];
        let tableNumber = '';
        let currentProductForModal = null;

        // URL'den masa numarasını al (en sondaki #masa ile başlayan kısmı bul)
        function getTableNumberFromUrl() {
            const hashes = window.location.hash.split('#');
            for (let i = hashes.length - 1; i >= 0; i--) {
                const h = hashes[i];
                if (h.startsWith('masa')) {
                    return h.replace('masa', '');
                }
            }
            return '';
        }

        // Masa numarasını localStorage'dan yükle ve kaydet
        function saveTableNumber(num) {
            localStorage.setItem('qrmenu_table', num);
        }
        function loadTableNumber() {
            const urlTableNumber = getTableNumberFromUrl();
            let stored = localStorage.getItem('qrmenu_table');
            if (urlTableNumber) {
                tableNumber = urlTableNumber;
                saveTableNumber(urlTableNumber);
            } else if (stored) {
                tableNumber = stored;
                // URL'de yoksa, URL'ye ekle
                if (!window.location.hash.includes('#masa')) {
                    window.location.hash = '#masa' + stored;
                }
            }
            if (tableNumber) {
                document.getElementById('tableNumber').value = tableNumber;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            loadTableNumber();
            updateCartDisplay();
            loadCart();
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const menuContent = document.getElementById('menuContent');
        const backToCategoriesBtn = document.getElementById('backToCategoriesBtn');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            
            if (query.length === 0) {
                searchResults.style.display = 'none';
                menuContent.style.display = 'block';
                backToCategoriesBtn.style.display = 'none';
                return;
            }

            if (query.length < 2) return;

            // Search through products
            const products = [];
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.querySelector('.product-title').textContent.toLowerCase();
                const description = card.querySelector('.product-description')?.textContent.toLowerCase() || '';
                
                if (name.includes(query) || description.includes(query)) {
                    const productData = {
                        name: card.querySelector('.product-title').textContent,
                        description: card.querySelector('.product-description')?.textContent || '',
                        price: card.querySelector('.add-to-cart')?.dataset.productPrice || '0',
                        image: card.querySelector('.add-to-cart')?.dataset.productImage || '',
                        id: card.querySelector('.add-to-cart')?.dataset.productId || '',
                        inStock: !card.querySelector('.add-to-cart')?.disabled
                    };
                    products.push(productData);
                }
            });

            displaySearchResults(products, query);
        });

        function displaySearchResults(products, query) {
            if (products.length === 0) {
                searchResults.innerHTML = `
                    <div class="search-result-item text-center">
                        <i class="bi bi-search text-muted"></i>
                        <p class="mb-0 text-muted">"${query}" için sonuç bulunamadı</p>
                    </div>
                `;
            } else {
                searchResults.innerHTML = products.map(product => `
                    <div class="search-result-item" onclick="addToCartFromSearch('${product.id}', '${product.name}', '${product.price}', '${product.image}', ${product.inStock})">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                ${product.image ? 
                                    `<img src="${product.image}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">` :
                                    `<div style="width: 50px; height: 50px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>`
                                }
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${product.name}</h6>
                                <p class="mb-1 text-muted small">${product.description.substring(0, 80)}${product.description.length > 80 ? '...' : ''}</p>
                                <span class="text-success fw-bold">${parseFloat(product.price).toFixed(2)} ₺</span>
                            </div>
                            <div>
                                ${product.inStock ? 
                                    '<i class="bi bi-cart-plus text-primary"></i>' : 
                                    '<i class="bi bi-x-circle text-muted"></i>'
                                }
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            searchResults.style.display = 'block';
            menuContent.style.display = 'none';
            backToCategoriesBtn.style.display = 'block';
        }

        function addToCartFromSearch(productId, productName, productPrice, productImage, inStock) {
            if (!inStock) return;
            
            if (!tableNumber) {
                showTableModal(() => {
                    addToCart(productId, productName, parseFloat(productPrice), productImage);
                });
            } else {
                addToCart(productId, productName, parseFloat(productPrice), productImage);
            }
            
            // Clear search
            searchInput.value = '';
            searchResults.style.display = 'none';
            menuContent.style.display = 'block';
            backToCategoriesBtn.style.display = 'none';
        }

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                // URL'den masa numarasını kontrol et
                const urlTableNumber = getTableNumberFromUrl();
                if (urlTableNumber && !tableNumber) {
                    tableNumber = urlTableNumber;
                    document.getElementById('tableNumber').value = urlTableNumber;
                }
                
                if (!tableNumber) {
                    showTableModal(() => {
                        addToCartFromButton(this);
                    });
                } else {
                    addToCartFromButton(this);
                }
            });
        });

        function addToCartFromButton(button) {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const productPrice = parseFloat(button.dataset.productPrice);
            const productImage = button.dataset.productImage;
            
            addToCart(productId, productName, productPrice, productImage);
            
            // Visual feedback
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Eklendi!';
            button.classList.remove('btn-add-cart');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-add-cart');
            }, 1500);
        }

        function addToCart(productId, productName, productPrice, productImage) {
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            saveCart();
            saveTableNumber(tableNumber);
        }

        function saveCart() {
            localStorage.setItem('qrmenu_cart', JSON.stringify(cart));
        }

        function loadCart() {
            const savedCart = localStorage.getItem('qrmenu_cart');
            
            if (savedCart) {
                cart = JSON.parse(savedCart);
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
            const cartFab = document.getElementById('cartFab');
            const cartBadge = document.getElementById('cartBadge');
            
            if (cartCount > 0) {
                cartFab.style.display = 'block';
                cartBadge.textContent = cartCount;
            } else {
                
            }
            
            updateCartModal();
        }

        function updateCartModal() {
            const cartItems = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            const cartTotal = document.getElementById('cartTotal');
            const placeOrder = document.getElementById('placeOrder');
            
            if (cart.length === 0) {
                cartItems.style.display = 'none';
                emptyCart.style.display = 'block';
                placeOrder.disabled = true;
                cartTotal.textContent = '0.00 ₺';
                return;
            }
            
            cartItems.style.display = 'block';
            emptyCart.style.display = 'none';
            placeOrder.disabled = false;
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `${total.toFixed(2)} ₺`;
            
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            ${item.image ? 
                                `<img src="${item.image}" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">` :
                                `<div style="width: 60px; height: 60px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.name}</h6>
                            <span class="text-success fw-bold">${item.price.toFixed(2)} ₺</span>
                        </div>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity('${item.id}', -1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="fw-bold mx-2">${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity('${item.id}', 1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <button class="btn btn-outline-danger btn-sm ms-3" onclick="removeFromCart('${item.id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCartDisplay();
                    saveCart();
                }
            }
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartDisplay();
            saveCart();
        }

        // Masa modalı ve inputu tamamen kaldırıldı
        function showTableModal(callback) {
            if (!tableNumber) {
                alert('Masa bilgisi bulunamadı, lütfen QR kodu tekrar okutun.');
                return;
            }
            if (typeof callback === 'function') callback();
        }

        // Product detail modal
        document.querySelectorAll('[data-bs-target="#productModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = document.getElementById('productModal');
                const title = modal.querySelector('#productModalTitle');
                const image = modal.querySelector('#productModalImage');
                const description = modal.querySelector('#productModalDescription');
                const price = modal.querySelector('#productModalPrice');
                
                title.textContent = this.dataset.productName;
                description.textContent = this.dataset.productDescription;
                price.textContent = `${parseFloat(this.dataset.productPrice).toFixed(2)} ₺`;
                
                if (this.dataset.productImage) {
                    image.style.backgroundImage = `url('${this.dataset.productImage}')`;
                } else {
                    image.style.background = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
                    image.innerHTML = '<i class="bi bi-image text-muted" style="font-size: 3rem; display: flex; align-items: center; justify-content: center; height: 100%;"></i>';
                }
                
                currentProductForModal = {
                    id: this.closest('.product-card').querySelector('.add-to-cart')?.dataset.productId,
                    name: this.dataset.productName,
                    price: parseFloat(this.dataset.productPrice),
                    image: this.dataset.productImage
                };
            });
        });

        document.getElementById('addFromModal').addEventListener('click', function() {
            if (currentProductForModal) {
                if (!tableNumber) {
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    showTableModal(() => {
                        addToCart(currentProductForModal.id, currentProductForModal.name, currentProductForModal.price, currentProductForModal.image);
                    });
                } else {
                    addToCart(currentProductForModal.id, currentProductForModal.name, currentProductForModal.price, currentProductForModal.image);
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                }
            }
        });

        // Place order
        const placeOrderBtn = document.getElementById('placeOrder');
        placeOrderBtn.addEventListener('click', function() {
            if (cart.length === 0) return;

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            if (!confirm(`Siparişinizi onaylıyor musunuz?\n\nMasa: ${tableNumber}\nToplam: ${total.toFixed(2)} ₺`)) {
                return;
            }

            placeOrderBtn.disabled = true;

            fetch('{{ route("menu.order.place", ["slug" => $restaurant->slug]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    table_number: tableNumber,
                    items: cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity,
                        note: item.note || null
                    }))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Siparişiniz başarıyla alındı! Teşekkür ederiz.');
                    // Sepeti ve masa numarasını temizle
                    cart = [];
                    tableNumber = '';
                    localStorage.removeItem('qrmenu_cart');
                    localStorage.removeItem('qrmenu_table');
                    updateCartDisplay();
                    bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Sunucu hatası oluştu, lütfen tekrar deneyin.');
            })
            .finally(() => {
                placeOrderBtn.disabled = false;
            });
        });

        // Local storage
        function saveCart() {
            localStorage.setItem('qrmenu_cart', JSON.stringify(cart));
        }

        function loadCart() {
            const savedCart = localStorage.getItem('qrmenu_cart');
            
            if (savedCart) {
                cart = JSON.parse(savedCart);
            }
            
            updateCartDisplay();
        }

        // Hash'ten masa ve kategori ayıklama fonksiyonları
        function extractMasaHash(hash) {
            const matches = hash.match(/#masa\w+/);
            return matches ? matches[0] : '';
        }
        function extractCategoryHash(hash) {
            const matches = hash.match(/#category-[^#]+/);
            return matches ? matches[0] : '';
        }

        // Global function for back to categories button
        function goBackToCategories() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const backToCategoriesBtn = document.getElementById('backToCategoriesBtn');
            const menuContent = document.getElementById('menuContent');
            const categoryGrid = document.getElementById('categoryGrid');
            
            window.location.hash = ''; // URL'den hash'i kaldır
            
            if (categoryGrid) categoryGrid.style.display = 'block';
            menuContent.style.display = 'none';
            searchInput.value = '';
            searchResults.style.display = 'none';
            backToCategoriesBtn.style.display = 'none';
        }

        // İlk açılışta kategori gridini göster, menüyü gizle
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            const welcomeScreen = document.getElementById('welcomeScreen');
            const menuContent = document.getElementById('menuContent');
            const categoryGrid = document.getElementById('categoryGrid');
            const backToCategoriesBtn = document.getElementById('backToCategoriesBtn');

            function showMenu() {
                if (welcomeScreen) welcomeScreen.style.display = 'none';
                if (categoryGrid) categoryGrid.style.display = 'block';
                if (menuContent) menuContent.style.display = 'none';
                if (backToCategoriesBtn) backToCategoriesBtn.style.display = 'none';
            }

            // Karşılama ekranı ilk açılışta gösterilsin
            if (welcomeScreen) {
                welcomeScreen.style.display = 'flex';
                if (menuContent) menuContent.style.display = 'none';
                if (categoryGrid) categoryGrid.style.display = 'none';
            }

            // Menü butonuna tıklanınca kategori gridine geç
            const welcomeMenuBtn = document.getElementById('welcomeMenuBtn');
            if (welcomeMenuBtn) {
                welcomeMenuBtn.addEventListener('click', function() {
                    showMenu();
                });
            }

            // Değerlendirme butonu (isteğe bağlı yönlendirme)
            const welcomeRateBtn = document.getElementById('welcomeRateBtn');
            if (welcomeRateBtn) {
                welcomeRateBtn.addEventListener('click', function() {
                    window.open('{{ $restaurant->rate_url ?? "#" }}', '_blank');
                });
            }

            // Dil seçici (isteğe bağlı, backend ile entegre edilebilir)
            const languageSelect = document.getElementById('languageSelect');
            if (languageSelect) {
                languageSelect.addEventListener('change', function() {
                    // Burada seçilen dile göre sayfa yenileme veya locale değişimi yapılabilir
                    // window.location.search = '?lang=' + this.value;
                });
            }

            // Kategori grid kartlarına tıklama
            document.querySelectorAll('.category-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const masaHash = extractMasaHash(window.location.hash);
                    if (targetId === 'all-products') {
                        menuContent.scrollIntoView({behavior: 'smooth'});
                    } else {
                        window.location.hash = '#' + targetId + masaHash;
                        if (categoryGrid) categoryGrid.style.display = 'none';
                        if (menuContent) menuContent.style.display = 'block';
                        if (backToCategoriesBtn) backToCategoriesBtn.style.display = 'block';
                        const target = document.getElementById(targetId);
                        if (target) {
                            setTimeout(() => target.scrollIntoView({behavior: 'smooth'}), 300);
                        }
                    }
                });
            });

            // Hash değişikliklerini dinle (tarayıcı geri butonu için)
            window.addEventListener('hashchange', function() {
                if (window.location.hash.startsWith('#category-')) {
                    if (categoryGrid) categoryGrid.style.display = 'none';
                    if (menuContent) menuContent.style.display = 'block';
                    if (backToCategoriesBtn) backToCategoriesBtn.style.display = 'block';
                    const target = document.querySelector(window.location.hash);
                    if (target) {
                        setTimeout(() => target.scrollIntoView({behavior: 'smooth'}), 300);
                    }
                } else {
                    if (categoryGrid) categoryGrid.style.display = 'block';
                    if (menuContent) menuContent.style.display = 'none';
                    if (backToCategoriesBtn) backToCategoriesBtn.style.display = 'none';
                }
            });
        });

        // Aktif siparişleri getir ve göster
        function fetchActiveOrders() {
            if (!tableNumber) return;
            fetch(`/api/menu/{{ $restaurant->slug }}/active-orders?table_number=${encodeURIComponent(tableNumber)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderActiveOrders(data.orders);
                    }
                });
        }
        // Aktif sipariş durumlarını Türkçe göster
        function renderActiveOrders(orders) {
            const area = document.getElementById('activeOrdersList');
            if (!orders || orders.length === 0) {
                area.innerHTML = '<div class="text-muted">Bu masada işlemde olan sipariş yok.</div>';
                return;
            }
            const statusMap = {
                'pending': 'Bekliyor',
                'preparing': 'Hazırlanıyor',
                'ready': 'Hazır'
            };
            area.innerHTML = orders.map(order => {
                let canCancel = (order.status === 'pending' || order.status === 'preparing');
                let items = order.order_items.map(item => `<li>${item.product ? item.product.name : 'Ürün'} x${item.quantity}</li>`).join('');
                return `<div class="border rounded p-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <b>#${order.id}</b> - <span class="badge bg-secondary">${statusMap[order.status] || order.status}</span>
                        </div>
                        ${canCancel ? `<button class="btn btn-sm btn-outline-danger" onclick="cancelOrder(${order.id})"><i class='bi bi-x-circle'></i> İptal Et</button>` : ''}
                    </div>
                    <ul class="mb-1">${items}</ul>
                    <div class="text-end small text-muted">${order.created_at}</div>
                </div>`;
            }).join('');
        }
        // Sepet modalı açıldığında aktif siparişleri getir
        document.getElementById('cartModal').addEventListener('show.bs.modal', fetchActiveOrders);
        // Sipariş iptal fonksiyonu
        function cancelOrder(orderId) {
            if (!confirm('Siparişi iptal etmek istediğinize emin misiniz?')) return;
            fetch(`/api/menu/{{ $restaurant->slug }}/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ table_number: tableNumber })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Sipariş iptal edildi.');
                    fetchActiveOrders();
                } else {
                    alert(data.message || 'İptal edilemedi.');
                }
            });
        }

        // Sepet butonuna tıklanınca masa yoksa uyarı ver
        const cartFab = document.getElementById('cartFab');
        if (cartFab) {
            cartFab.addEventListener('click', function(e) {
                // Try to get table number from URL hash
                let urlTableNumber = getTableNumberFromUrl();
                if (!urlTableNumber) {
                    // Try to get from localStorage
                    let stored = localStorage.getItem('qrmenu_table');
                    if (stored) {
                        // Restore to URL hash and variable
                        window.location.hash = '#masa' + stored;
                        tableNumber = stored;
                        // Optionally, update input field if present
                        if (document.getElementById('tableNumber')) {
                            document.getElementById('tableNumber').value = stored;
                        }
                        // Allow cart modal to open
                        return;
                    } else {
                        e.preventDefault();
                        alert('Masa bilgisi bulunamadı, lütfen QR kodu tekrar okutun.');
                        return false;
                    }
                } else {
                    // Table number found in URL, update variable
                    tableNumber = urlTableNumber;
                }
            });
        }
    </script>
</body>
</html>