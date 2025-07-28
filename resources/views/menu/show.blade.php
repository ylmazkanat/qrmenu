<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }} - Menü</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $restaurant->primary_color ?? '#6366f1' }};
            --secondary-color: {{ $restaurant->secondary_color ?? '#8b5cf6' }};
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
        
        /* Masaüstü ve tablet için iPhone 12 görünümü */
        @media (min-width: 768px) {
            body {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .mobile-container {
                width: 390px;
                max-width: 390px;
                height: 844px;
                max-height: 844px;
                background: white;
                border-radius: 47px;
                box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
                overflow: hidden;
                position: relative;
                border: 8px solid #1a1a1a;
                border-top: 30px solid #1a1a1a;
                border-bottom: 30px solid #1a1a1a;
            }
            
            .mobile-container::before {
                content: '';
                position: absolute;
                top: -15px;
                left: 50%;
                transform: translateX(-50%);
                width: 134px;
                height: 30px;
                background: #1a1a1a;
                border-radius: 0 0 20px 20px;
                z-index: 10;
            }
            
            /* iPhone 12 notch (çentik) */
            .mobile-container::after {
                content: '';
                position: absolute;
                top: 8px;
                left: 50%;
                transform: translateX(-50%);
                width: 126px;
                height: 30px;
                background: #1a1a1a;
                border-radius: 0 0 20px 20px;
                z-index: 11;
            }
            
            /* iPhone 12 alt çubuk */
            .mobile-container .bottom-indicator {
                content: '';
                position: absolute;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
                width: 134px;
                height: 5px;
                background: #1a1a1a;
                border-radius: 3px;
                z-index: 10;
            }
            
            .mobile-screen {
                width: 100%;
                height: 100%;
                overflow-y: auto;
                overflow-x: hidden;
                position: relative;
                background: white;
                display: flex;
                flex-direction: column;
            }
            
            /* Mobil telefon içinde tüm içeriği düzgün göster */
            .mobile-screen .restaurant-header {
                flex-shrink: 0;
                position: relative;
                z-index: 1000;
                transition: transform 0.3s ease;
            }
            
            /* Kaydırma sırasında header'ı gizle */
            .mobile-screen .restaurant-header.header-hidden {
                transform: translateY(-100%);
            }
            
            .mobile-screen .search-section {
                flex-shrink: 0;
            }
            
            .mobile-screen .category-nav-menu {
                flex-shrink: 0;
            }
            
            .mobile-screen #categoryGrid,
            .mobile-screen #menuContent,
            .mobile-screen #searchResults {
                flex: 1;
                overflow-y: auto;
                padding-bottom: 120px; /* Alt navigasyon menüsü için alan */
            }
            
            .mobile-screen .bottom-nav {
                flex-shrink: 0;
                position: relative;
                z-index: 1000;
            }
            
            /* Alt navigasyon menüsü için düzeltmeler */
            .mobile-screen .mobile-nav-menu {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid #e5e7eb;
                z-index: 1002;
                padding: 10px 0;
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
            
            .mobile-screen .category-nav-menu {
                position: absolute;
                bottom: 70px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 1001;
            }
            
            .mobile-screen .cart-fab {
                position: absolute;
                bottom: 90px;
                right: 20px;
                z-index: 1003;
            }
            
            /* Ürün kartları için düzeltmeler */
            .mobile-screen .product-card {
                margin-bottom: 15px;
                width: 100%;
            }
            
            /* Grid düzenini tek sütun yap */
            .mobile-screen .products-grid.grid-view {
                display: flex !important;
                flex-direction: column !important;
                gap: 15px !important;
            }
            
            .mobile-screen .products-grid.grid-view .product-card {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            .mobile-screen .product-image {
                height: 120px;
            }
            
            .mobile-screen .product-content {
                padding: 12px;
            }
            
            .mobile-screen .product-title {
                font-size: 1rem;
                margin-bottom: 8px;
            }
            
            .mobile-screen .product-description {
                font-size: 0.85rem;
                margin-bottom: 12px;
            }
            
            .mobile-screen .product-actions {
                gap: 8px;
            }
            
            .mobile-screen .product-actions .btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
            
            /* Masaüstü ve tablette kategoriler 2'şerli grid olsun */
            .mobile-screen #categoryGrid .col-lg-4,
            .mobile-screen #categoryGrid .col-md-6,
            .mobile-screen #categoryGrid .col-6 {
                width: 50% !important;
                max-width: 50% !important;
                flex: 0 0 50% !important;
            }
            
            /* Ürünler tek sütun kalsın */
            .mobile-screen #menuContent .col-lg-4,
            .mobile-screen #menuContent .col-md-6,
            .mobile-screen #menuContent .col-6 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            
            /* Alt menülerin pozisyonunu sabitle */
            .mobile-screen .mobile-nav-menu {
                position: absolute !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                transform: none !important;
            }
            
            .mobile-screen .category-nav-menu {
                position: absolute !important;
                bottom: 70px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            /* Kategoriye girildiğinde alt menülerin pozisyonunu koru */
            .mobile-screen #menuContent ~ .mobile-nav-menu,
            .mobile-screen #menuContent ~ .category-nav-menu {
                position: absolute !important;
                bottom: 70px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            .mobile-screen #menuContent ~ .mobile-nav-menu {
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                transform: none !important;
            }
            
            /* Scroll bar stilleri */
            .mobile-screen::-webkit-scrollbar {
                width: 6px;
            }
            
            .mobile-screen::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }
            
            .mobile-screen::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }
            
            .mobile-screen::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
            
            /* İçerik alanları için scroll bar */
            .mobile-screen #categoryGrid::-webkit-scrollbar,
            .mobile-screen #menuContent::-webkit-scrollbar,
            .mobile-screen #searchResults::-webkit-scrollbar {
                width: 4px;
            }
            
            .mobile-screen #categoryGrid::-webkit-scrollbar-track,
            .mobile-screen #menuContent::-webkit-scrollbar-track,
            .mobile-screen #searchResults::-webkit-scrollbar-track {
                background: #f8f9fa;
                border-radius: 2px;
            }
            
            .mobile-screen #categoryGrid::-webkit-scrollbar-thumb,
            .mobile-screen #menuContent::-webkit-scrollbar-thumb,
            .mobile-screen #searchResults::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 2px;
            }
        }
        
        /* Mobil cihazlarda normal görünüm */
        @media (max-width: 767px) {
            .mobile-container {
                width: 100%;
                max-width: none;
                height: auto;
                max-height: none;
                border-radius: 0;
                box-shadow: none;
                border: none;
            }
            
            .mobile-screen {
                width: 100%;
                height: auto;
                overflow: visible;
            }
        }

        .restaurant-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
            min-height: 250px;
            display: flex;
            align-items: center;
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

        .restaurant-logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .restaurant-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .restaurant-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .restaurant-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .restaurant-icon i {
            font-size: 3rem;
            color: white;
        }

        .restaurant-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 0;
        }

        .restaurant-description {
             font-size: 1.1rem;
             opacity: 0.9;
             text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
             max-width: 600px;
             margin: 0 auto;
         }

        @media (max-width: 768px) {
            .restaurant-title {
                font-size: 2rem;
            }
            
            .restaurant-logo, .restaurant-icon {
                width: 80px;
                height: 80px;
            }
            
            .restaurant-icon i {
                font-size: 2rem;
            }
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
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            
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
        
        /* Kategori grid için sadece 2'şerli grid düzeni */
        #categoryGrid .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        #categoryGrid .col-6,
        #categoryGrid .col-md-4,
        #categoryGrid .col-lg-3 {
            padding: 0 10px;
            width: 50%;
            max-width: 50%;
            flex: 0 0 50%;
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
            height: 160px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: visible;
        }

        .product-image::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.3));
            border-radius: 12px;
        }

        .product-price {
            position: absolute;
            background: var(--primary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Grid view için product price */
        .products-grid.grid-view .product-price {
            top: 12px;
            right: 12px;
            font-size: 12px;
            padding: 6px 10px;
        }
        
        /* List view için product price */
        .products-grid.list-view .product-price {
            top: -10px;
            left: -10px;
            font-size: 10px;
            padding: 4px 8px;
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
            padding: 1rem;
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
            margin-bottom: 10px;
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
            background: var(--primary-color);
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
            background: var(--secondary-color);
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
    </style>
    
    <script>
        function ensureMasaHashAtEnd() {
            // If tableNumber exists, make sure #masaXX is at the end of the hash
            if (tableNumber) {
                let hash = window.location.hash;
                let masaHash = `#masa${tableNumber}`;
                // Remove any existing masa hash
                hash = hash.replace(/#masa\w+/g, '');
                // Remove trailing #
                hash = hash.replace(/#+$/, '');
                // Add masa hash at the end
                window.location.hash = (hash ? hash : '') + masaHash;
            }
        }
        window.addEventListener('hashchange', ensureMasaHashAtEnd);
        document.addEventListener('DOMContentLoaded', function() {
            loadTableNumber();
            updateCartDisplay();
            loadCart();
            ensureMasaHashAtEnd();
        });
    </script>
</head>
<body>
        <!-- Mobil telefon container - sadece masaüstü ve tablet için -->
    <div class="mobile-container">
        <div class="bottom-indicator"></div>
        <div class="mobile-screen">
    <!-- Restaurant Header -->
    <div class="restaurant-header" @if($restaurant->header_image) style="background-image: url('{{ Storage::url($restaurant->header_image) }}'); background-size: cover; background-position: center;" @endif>
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <div class="restaurant-logo-container mb-3">
                        @if($restaurant->logo)
                            <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="restaurant-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="restaurant-icon" style="display: none;">
                                <i class="bi bi-shop"></i>
                            </div>
                        @else
                            <div class="restaurant-icon">
                                <i class="bi bi-shop"></i>
                            </div>
                        @endif
                    </div>
                    <h1 class="mb-0 restaurant-title">
                        {{ $restaurant->name }}
                    </h1>
                    @if($restaurant->description)
                        <p class="restaurant-description mt-2">{{ $restaurant->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="container">
            <div class="d-flex gap-2 gap-md-3 align-items-center">
                <!-- Kategorilere Git Butonu -->
                <button class="btn btn-outline-primary btn-sm flex-shrink-0" id="backToCategoriesBtn" style="display: none; font-size: 0.7rem; padding: 0.2rem 0.4rem; width: auto;" onclick="goBackToCategories()">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span class="d-none d-md-inline">Kategorilere Dön</span>
                    <span class="d-md-none">Geri</span>
                </button>
                
                <!-- Arama Kutusu -->
                <div class="search-box flex-grow-1">
                    <input type="text" class="search-input" id="searchInput" placeholder="Ürün ara...">
                    <i class="bi bi-search search-icon"></i>
                </div>
                
                <!-- Layout Toggle Buttons -->
                <div class="layout-toggle-container">
                    <button class="layout-toggle-btn active" id="gridViewBtn" data-layout="grid">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button class="layout-toggle-btn" id="listViewBtn" data-layout="list">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
            
            <!-- Search Results -->
            <div id="searchResults" class="search-results" style="display: none;"></div>
        </div>
    </div>

    <!-- Category Grid -->
    @if($restaurant->categories->count() > 0)
        <div class="container py-5" id="categoryGrid">
            <div class="row products-grid">
                <!-- Tümü Seçeneği -->
                <div class="col-6 col-md-4 col-lg-3 mb-3">
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
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
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
                        <i class="bi bi-bookmark-fill me-2" style="color: var(--primary-color);"></i>
                        {{ $category->name }}
                    </h3>
                    
                    @if($category->products->count() > 0)
                        <div class="row products-grid grid-view">
                            @foreach($category->products as $product)
                                <div class="col-md-6 col-lg-4 mb-3">
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
    <button class="cart-fab" id="cartFab" style="display: block;" data-bs-toggle="modal" data-bs-target="#cartModal">
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
                    <div id="productModalImage" class="mb-3" style="height: 200px; border-radius: 12px; background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                    <p id="productModalDescription" class="text-muted"></p>
                    <h4 id="productModalPrice" style="color: var(--primary-color);"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn" id="addFromModal" style="background: var(--primary-color); border-color: var(--primary-color); color: white;">
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
                        <div id="activeOrdersList" class="mb-2" style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Toplam:</h5>
                            <h4 class="mb-0" id="cartTotal" style="color: var(--primary-color);">0.00 ₺</h4>
                        </div>
                        <button type="button" class="btn btn-lg w-100" id="placeOrder" disabled style="background: var(--primary-color); border-color: var(--primary-color); color: white;">
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
                    // Fiyat bilgisini product-price elementinden al
                    const priceElement = card.querySelector('.product-price');
                    const price = priceElement ? priceElement.textContent.replace('₺', '').trim() : '0';
                    
                    // Görsel bilgisini product-image elementinden al
                    const imageElement = card.querySelector('.product-image');
                    let image = '';
                    if (imageElement && imageElement.style.backgroundImage) {
                        const bgImage = imageElement.style.backgroundImage;
                        if (bgImage !== 'none' && bgImage.includes('url(')) {
                            image = bgImage.replace(/url\(['"]?(.*?)['"]?\)/g, '$1');
                        }
                    }
                    
                    const productData = {
                        name: card.querySelector('.product-title').textContent,
                        description: card.querySelector('.product-description')?.textContent || '',
                        price: price,
                        image: image,
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
                searchResults.innerHTML = `
                    <div class="container py-4">
                        <h4 class="mb-4">
                            <i class="bi bi-search me-2" style="color: var(--primary-color);"></i>
                            "${query}" için ${products.length} sonuç bulundu
                        </h4>
                        <div class="row products-grid list-view">
                            ${products.map(product => `
                                <div class="col-12 mb-3">
                                    <div class="product-card fade-in">
                                        <div class="product-image" 
                                             ${product.image && product.image !== '' ? 
                                                `style="background: #f8f9fa url('${product.image}') center/cover no-repeat;"` :
                                                `style="background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center;"`
                                             }>
                                            
                                            ${!product.image || product.image === '' ? '<i class="bi bi-image text-muted" style="font-size: 3rem;"></i>' : ''}
                                            
                                            <div class="product-price">${parseFloat(product.price).toFixed(2)} ₺</div>
                                            
                                            ${!product.inStock ? '<div class="product-badge bg-danger">Tükendi</div>' : ''}
                                        </div>
                                        
                                        <div class="product-content">
                                            <h5 class="product-title">${product.name}</h5>
                                            
                                            ${product.description ? `<p class="product-description">${product.description}</p>` : ''}
                                            
                                            <div class="product-actions">
                                                ${product.inStock ? 
                                                    `<button class="btn btn-add-cart btn-modern add-to-cart" 
                                                             data-product-id="${product.id}"
                                                             data-product-name="${product.name}"
                                                             data-product-price="${product.price}"
                                                             data-product-image="${product.image || ''}">
                                                        <i class="bi bi-cart-plus me-2"></i>
                                                        Sepete Ekle
                                                    </button>` : 
                                                    `<button class="btn btn-secondary btn-modern" disabled>
                                                        <i class="bi bi-x-circle me-2"></i>
                                                        Tükendi
                                                    </button>`
                                                }
                                                
                                                ${product.description && product.description.length > 50 ? 
                                                    `<button class="btn btn-detail btn-modern" 
                                                             data-bs-toggle="modal" 
                                                             data-bs-target="#productModal"
                                                             data-product-name="${product.name}"
                                                             data-product-description="${product.description}"
                                                             data-product-price="${product.price}"
                                                             data-product-image="${product.image || ''}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>` : ''
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            searchResults.style.display = 'block';
            menuContent.style.display = 'none';
            backToCategoriesBtn.style.display = 'block';
            
            // Arama sonuçlarındaki butonlara event listener ekle
            setTimeout(() => {
                searchResults.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        addToCartFromButton(this);
                    });
                });
                
                // Detail butonları için event listener ekle
                searchResults.querySelectorAll('[data-bs-target="#productModal"]').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Modal verilerini ayarla
                        const modal = document.getElementById('productModal');
                        const title = modal.querySelector('#productModalTitle');
                        const image = modal.querySelector('#productModalImage');
                        const description = modal.querySelector('#productModalDescription');
                        const price = modal.querySelector('#productModalPrice');
                        
                        title.textContent = this.dataset.productName;
                        description.textContent = this.dataset.productDescription;
                        price.textContent = `${parseFloat(this.dataset.productPrice).toFixed(2)} ₺`;
                        
                        if (this.dataset.productImage && this.dataset.productImage !== '') {
                            image.style.backgroundImage = `url('${this.dataset.productImage}')`;
                            image.style.backgroundSize = 'cover';
                            image.style.backgroundPosition = 'center';
                            image.innerHTML = '';
                        } else {
                            image.style.background = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
                            image.style.backgroundImage = 'none';
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
            }, 100);
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
                            <span class="fw-bold" style="color: var(--primary-color);">${item.price.toFixed(2)} ₺</span>
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
                    image.style.backgroundSize = 'cover';
                    image.style.backgroundPosition = 'center';
                    image.innerHTML = ''; // Resim varsa icon'u temizle
                } else {
                    image.style.background = 'linear-gradient(135deg, #f3f4f6, #e5e7eb)';
                    image.style.backgroundImage = 'none';
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
            const menuContent = document.getElementById('menuContent');
            const categoryGrid = document.getElementById('categoryGrid');

            function showMenu() {
                if (categoryGrid) categoryGrid.style.display = 'none';
                menuContent.style.display = 'block';
                backToCategoriesBtn.style.display = 'block'; // Menüdeyken butonu göster
            }

            function showCategoryGrid() {
                if (categoryGrid) categoryGrid.style.display = 'block';
                menuContent.style.display = 'none';
                backToCategoriesBtn.style.display = 'none'; // Kategori grid'inde butonu gizle
            }

            // Eğer URL hash ile geldiysek menüyü göster ve o kategoriye kaydır
            if (window.location.hash.startsWith('#category-')) {
                showMenu();
                const target = document.querySelector(window.location.hash);
                if (target) {
                    setTimeout(() => target.scrollIntoView({behavior: 'smooth'}), 300);
                }
            } else {
                // Hash yoksa kategori gridini göster
                showCategoryGrid();
            }

            // Hash değişikliklerini dinle (tarayıcı geri butonu için)
            window.addEventListener('hashchange', function() {
                if (window.location.hash.startsWith('#category-')) {
                    showMenu();
                    const target = document.querySelector(window.location.hash);
                    if (target) {
                        // Tüm kategorileri göster
                        document.querySelectorAll('.category-section').forEach(section => {
                            section.style.display = 'block';
                        });
                        setTimeout(() => target.scrollIntoView({behavior: 'smooth'}), 300);
                    }
                } else {
                    showCategoryGrid();
                }
            });

            // Grid kartlarına tıklama
            document.querySelectorAll('.category-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const masaHash = extractMasaHash(window.location.hash);
                    // Eğer "Tümü" seçeneğine tıklandıysa
                    if (targetId === 'all-products') {
                        window.location.hash = masaHash;
                        showMenu();
                        // Tüm kategorileri göster
                        document.querySelectorAll('.category-section').forEach(section => {
                            section.style.display = 'block';
                        });
                        // En üste kaydır
                        menuContent.scrollIntoView({behavior: 'smooth'});
                    } else {
                        // Belirli kategori seçildiyse
                        window.location.hash = '#' + targetId + masaHash;
                        showMenu();
                        const target = document.getElementById(targetId);
                        if (target) {
                            // Tüm kategorileri göster
                            document.querySelectorAll('.category-section').forEach(section => {
                                section.style.display = 'block';
                            });
                            // Seçilen kategoriye scroll et
                            setTimeout(() => target.scrollIntoView({behavior: 'smooth'}), 300);
                        }
                    }
                });
            });
            // Sayfa açılışında ve hash değişiminde masa hash'i en sonda değilse, en sona taşı
            function ensureMasaHashAtEnd() {
                const hash = window.location.hash;
                const masaHash = extractMasaHash(hash);
                const categoryHash = extractCategoryHash(hash);
                if (masaHash && (!hash.endsWith(masaHash) || (categoryHash && hash.indexOf(masaHash) < hash.indexOf(categoryHash)))) {
                    // Masa hash'i en sonda değilse, düzelt
                    window.location.hash = (categoryHash ? categoryHash : '') + masaHash;
                }
            }
            ensureMasaHashAtEnd();
            window.addEventListener('hashchange', ensureMasaHashAtEnd);
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

    <!-- Category Navigation Menu -->
    <div class="category-nav-menu">
        <div class="category-nav-container">
            <div class="category-nav-item active" data-category="all">
                <span>Tümü</span>
            </div>
            @foreach($restaurant->categories as $category)
                @php $catSlug = $category->slug ?: \Illuminate\Support\Str::slug($category->name.'-'.$category->id); @endphp
                <div class="category-nav-item" data-category="{{ $catSlug }}">
                    <span>{{ $category->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-nav-menu">
        <div class="nav-item" onclick="goToHome()">
            <i class="bi bi-journal-text"></i>
            <span>Menü</span>
        </div>
        <div class="nav-item" data-bs-toggle="modal" data-bs-target="#reviewModal">
            <i class="bi bi-star-fill"></i>
            <span>Değerlendirme</span>
        </div>
        <div class="nav-item" data-bs-toggle="modal" data-bs-target="#socialModal">
            <i class="bi bi-share-fill"></i>
            <span>Sosyal Medya</span>
        </div>
        <div class="nav-item" data-bs-toggle="modal" data-bs-target="#contactModal">
            <i class="bi bi-telephone-fill"></i>
            <span>İletişim</span>
        </div>
    </div>

    <!-- Modals -->
    <!-- Menu Info Modal -->
    <div class="modal fade" id="menuInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-journal-text me-2"></i>Menü Bilgileri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        @if($restaurant->logo)
                            <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: var(--primary-color);">
                                <i class="bi bi-shop text-white" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        <h4 class="mt-2">{{ $restaurant->name }}</h4>
                        @if($restaurant->description)
                            <p class="text-muted">{{ $restaurant->description }}</p>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-grid-3x3-gap" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h6 class="mt-2 mb-0">{{ $restaurant->categories->count() }}</h6>
                                <small class="text-muted">Kategori</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-cup-hot" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h6 class="mt-2 mb-0">{{ $restaurant->categories->sum(function($cat) { return $cat->products->count(); }) }}</h6>
                                <small class="text-muted">Ürün</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-star-fill me-2"></i>Değerlendirme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h4>{{ $restaurant->name }}</h4>
                        <p class="text-muted">Deneyiminizi bizimle paylaşın</p>
                    </div>
                    <form id="reviewForm" action="{{ route('menu.review.store', $restaurant->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="rating" id="selectedRating" value="0">
                        <div class="mb-3">
                            <label class="form-label">Puanınız</label>
                            <div class="rating-stars text-center">
                                <i class="bi bi-star star-rating" data-rating="1"></i>
                                <i class="bi bi-star star-rating" data-rating="2"></i>
                                <i class="bi bi-star star-rating" data-rating="3"></i>
                                <i class="bi bi-star star-rating" data-rating="4"></i>
                                <i class="bi bi-star star-rating" data-rating="5"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yorumunuz</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Deneyiminizi paylaşın..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İsminiz (İsteğe bağlı)</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Adınız">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-posta (İsteğe bağlı)</label>
                            <input type="email" name="customer_email" class="form-control" placeholder="E-posta adresiniz">
                        </div>
                        <button type="submit" class="btn w-100" style="background: var(--primary-color); border-color: var(--primary-color); color: white;">Değerlendirme Gönder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media Modal -->
    <div class="modal fade" id="socialModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-share-fill me-2"></i>Sosyal Medya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h4>{{ $restaurant->name }}</h4>
                        <p class="text-muted">Bizi takip edin ve paylaşın</p>
                    </div>
                    
                    <!-- Sosyal Medya Linkleri -->
                    <div class="row g-3 mb-4">
                        @if($restaurant->facebook)
                            <div class="col-6">
                                <a href="{{ $restaurant->facebook }}" target="_blank" class="btn btn-outline-primary w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-facebook me-2"></i>Facebook
                                </a>
                            </div>
                        @endif
                        @if($restaurant->instagram)
                            <div class="col-6">
                                <a href="{{ $restaurant->instagram }}" target="_blank" class="btn btn-outline-danger w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-instagram me-2"></i>Instagram
                                </a>
                            </div>
                        @endif
                        @if($restaurant->twitter)
                            <div class="col-6">
                                <a href="{{ $restaurant->twitter }}" target="_blank" class="btn btn-outline-info w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-twitter me-2"></i>Twitter
                                </a>
                            </div>
                        @endif
                        @if($restaurant->youtube)
                            <div class="col-6">
                                <a href="{{ $restaurant->youtube }}" target="_blank" class="btn btn-outline-danger w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-youtube me-2"></i>YouTube
                                </a>
                            </div>
                        @endif
                        @if($restaurant->linkedin)
                            <div class="col-6">
                                <a href="{{ $restaurant->linkedin }}" target="_blank" class="btn btn-outline-primary w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-linkedin me-2"></i>LinkedIn
                                </a>
                            </div>
                        @endif
                        @if($restaurant->whatsapp)
                            <div class="col-6">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $restaurant->whatsapp) }}" target="_blank" class="btn btn-outline-success w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-whatsapp me-2"></i>WhatsApp
                                </a>
                            </div>
                        @endif
                        @if($restaurant->website)
                            <div class="col-6">
                                <a href="{{ $restaurant->website }}" target="_blank" class="btn btn-outline-secondary w-100" style="border-color: var(--primary-color); color: var(--primary-color);">
                                    <i class="bi bi-globe me-2"></i>Website
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Sosyal Medya Linkleri Yoksa -->
                    @if(!$restaurant->facebook && !$restaurant->instagram && !$restaurant->twitter && !$restaurant->youtube && !$restaurant->linkedin && !$restaurant->whatsapp && !$restaurant->website)
                        <div class="text-center text-muted mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Henüz sosyal medya linkleri eklenmemiş.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-telephone-fill me-2"></i>İletişim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        @if($restaurant->logo)
                            <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                        @endif
                        <h4 class="mt-2">{{ $restaurant->name }}</h4>
                    </div>
                    @if($restaurant->phone)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="bi bi-telephone-fill me-3" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h6 class="mb-0">Telefon</h6>
                                <a href="tel:{{ $restaurant->phone }}" class="text-decoration-none">{{ $restaurant->phone }}</a>
                            </div>
                        </div>
                    @endif
                    @if($restaurant->email)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="bi bi-envelope-fill me-3" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h6 class="mb-0">E-posta</h6>
                                <a href="mailto:{{ $restaurant->email }}" class="text-decoration-none">{{ $restaurant->email }}</a>
                            </div>
                        </div>
                    @endif
                    @if($restaurant->website)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="bi bi-globe me-3" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h6 class="mb-0">Website</h6>
                                <a href="{{ $restaurant->website }}" target="_blank" class="text-decoration-none">{{ $restaurant->website }}</a>
                            </div>
                        </div>
                    @endif
                    @if($restaurant->address)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="bi bi-geo-alt-fill me-3" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h6 class="mb-0">Adres</h6>
                                <p class="mb-0 text-muted">{{ $restaurant->address }}</p>
                            </div>
                        </div>
                    @endif
                    @if($restaurant->working_hours_text)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <i class="bi bi-clock-fill me-3" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <div>
                                <h6 class="mb-0">Çalışma Saatleri</h6>
                                <p class="mb-0 text-muted">{!! nl2br(e($restaurant->working_hours_text)) !!}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



    <style>
     

        .category-nav-container {
            display: flex;
            overflow-x: auto;
            padding: 0 15px;
            gap: 15px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .category-nav-container::-webkit-scrollbar {
            display: none;
        }

        .category-nav-item {
            flex-shrink: 0;
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .category-nav-item:hover {
            background: var(--primary-color);
            color: white;
        }

        .category-nav-item.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .category-nav-item span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .mobile-nav-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            padding: 6px 0;
            z-index: 1002;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 6px 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            min-width: 50px;
        }

        .nav-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .nav-item.active {
            background: var(--primary-color);
            color: white;
        }

        .nav-item i {
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .nav-item span {
            font-size: 0.6rem;
            font-weight: 500;
            text-align: center;
        }

        .rating-stars {
            font-size: 2rem;
            margin: 1rem 0;
        }

        .star-rating {
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
            margin: 0 2px;
        }

        .star-rating:hover,
        .star-rating.active {
            color: #ffc107;
        }

        /* Sepet butonunu mobile nav için yukarı taşı */
        .cart-fab {
            bottom: 135px !important;
        }

        /* Sayfa içeriğine padding ekle */
        body {
            padding-bottom: 160px;
            overflow-y: auto !important;
        }
        
        /* Restaurant header'ı daha kompakt yap */
        .restaurant-header {
            margin-top: 0;
            padding: 15px 0;
            border-radius: 0 0 20px 20px;
            overflow: hidden;
        }
        
        .restaurant-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }
        
        .restaurant-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            border: 2px solid var(--primary-color);
            overflow: hidden;
        }
        
        .restaurant-title {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .restaurant-description {
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        /* Search section'ı göster */
        .search-section {
            display: block;
            padding: 10px 0;
            background: #f8f9fa;
        }
        
        /* Arama inputunu incelt */
        .search-input {
            height: 40px !important;
            font-size: 14px !important;
            padding: 8px 12px !important;
        }
        
        /* Category Navigation Menu - Fixed Bottom */
        .category-nav-menu {
            position: fixed;
            bottom: 65px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid #e5e7eb;
            z-index: 1001;
            padding: 8px;
            height: auto;
            max-height: 50px;
            max-width: 85%;
            width: 100%;
            border-radius: 25px 25px 0 0;
            overflow: hidden;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
        }
        
        /* Scroll indicator */
        .category-nav-menu::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
            opacity: 0.8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        

         
         /* Layout Toggle Buttons */
         .layout-toggle-container {
             display: flex;
             background: rgba(255, 255, 255, 0.95);
             border: 1px solid #e5e7eb;
             border-radius: 8px;
             padding: 2px;
             gap: 2px;
         }
         
         .layout-toggle-btn {
             width: 24px;
             height: 24px;
             border: none;
             background: transparent;
             border-radius: 4px;
             display: flex;
             align-items: center;
             justify-content: center;
             cursor: pointer;
             transition: all 0.2s ease;
             color: #6b7280;
             font-size: 12px;
         }
         
         .layout-toggle-btn:hover {
             background: #f3f4f6;
             color: var(--primary-color);
         }
         
         .layout-toggle-btn.active {
             background: var(--primary-color);
             color: white;
         }
         
         /* Product Grid Layouts */
         .products-grid.grid-view {
             display: grid;
             grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
             gap: 15px;
         }
         
         .products-grid.list-view {
             display: flex;
             flex-direction: column;
             gap: 10px;
         }
         
         .products-grid.list-view .product-card {
             display: flex;
             flex-direction: row;
             align-items: center;
             padding: 0px 12px 0px 0px;
         }
         
         .products-grid.list-view .product-image {
             width: 30%;
             height: 130px;
             margin-right: 15px;
             flex-shrink: 0;
             border-radius: 12px;
         }
         
         .products-grid.list-view .product-content {
             width: 70%;
             padding: 0;
             
         }
         
         .products-grid.list-view .product-title {
             font-size: 1rem;
             margin-bottom: 0.3rem;
             margin-top: 10px;
         }
         
         .products-grid.list-view .product-description {
             font-size: 0.8rem;
             margin-bottom: 0.5rem;
             -webkit-line-clamp: 2;
         }
         
         /* List view'da butonları küçült */
         .products-grid.list-view .product-actions .btn {
             font-size: 0.75rem;
             padding: 0.25rem 0.5rem;
             margin-right: 0.25rem;
         }
         
         .products-grid.list-view .product-actions .btn i {
             font-size: 0.8rem;
         }
         
         /* Modal butonları için hover efekti */
         #addFromModal:hover {
             background: var(--secondary-color) !important;
             border-color: var(--secondary-color) !important;
             transform: translateY(-1px);
             box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
         }
         
                 /* Modal iyileştirmeleri */
        .cursor-pointer {
            cursor: pointer;
        }
        
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }
        
        /* Aktif siparişler scroll alanı */
        #activeOrdersList {
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f1f1f1;
        }
        
        #activeOrdersList::-webkit-scrollbar {
            width: 6px;
        }
        
        #activeOrdersList::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        #activeOrdersList::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        #activeOrdersList::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        
        .category-nav-container {
            display: flex;
            overflow-x: auto;
            padding: 0 10px;
            gap: 8px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            height: 100%;
            align-items: center;
            scroll-behavior: smooth;
            white-space: nowrap;
        }
        
        .category-nav-container::-webkit-scrollbar {
            display: none;
        }
        
        .category-nav-item {
            flex-shrink: 0;
            padding: 6px 12px;
            background: #f8f9fa;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            white-space: nowrap;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
        }
        
        .category-nav-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }
        
        .category-nav-item.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .category-nav-item span {
            font-size: 12px;
            font-weight: 500;
        }
    </style>

    <script>
        // Rating stars functionality
        document.querySelectorAll('.star-rating').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('selectedRating').value = rating;
                
                document.querySelectorAll('.star-rating').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('active');
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
        });

        // Social sharing functions
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(`{{ $restaurant->name }} menüsüne göz atın!`);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(`{{ $restaurant->name }} menüsüne göz atın! ${window.location.href}`);
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link kopyalandı!');
            });
        }

        function scrollToTop() {
            window.scrollTo({ 
                top: 0, 
                behavior: 'smooth' 
            });
        }

        function goToHome() {
            // Ana sayfaya git (kategorileri göster)
            document.getElementById('categoryGrid').style.display = 'block';
            document.getElementById('menuContent').style.display = 'none';
            document.getElementById('backToCategoriesBtn').style.display = 'none';
            
            // URL hash'i temizle
            window.location.hash = '';
            
            // Kategori navigasyonunda "Tümü"nü aktif yap
            document.querySelectorAll('.category-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            const allCategoryItem = document.querySelector('.category-nav-item[data-category="all"]');
            if (allCategoryItem) {
                allCategoryItem.classList.add('active');
            }
            
            // Sayfanın en üstüne git
            scrollToTop();
        }
        
        function showCategory(categorySlug) {
            // Kategori gridini gizle, menüyü göster
            document.getElementById('categoryGrid').style.display = 'none';
            document.getElementById('menuContent').style.display = 'block';
            document.getElementById('backToCategoriesBtn').style.display = 'block';
            
            // URL hash'i güncelle
            window.location.hash = '#category-' + categorySlug;
        }
        
        function showAllCategories() {
            // Kategori gridini gizle, menüyü göster
            document.getElementById('categoryGrid').style.display = 'none';
            document.getElementById('menuContent').style.display = 'block';
            document.getElementById('backToCategoriesBtn').style.display = 'block';
            
            // Tüm kategorileri göster
            document.querySelectorAll('.category-section').forEach(section => {
                section.style.display = 'block';
            });
            
            // URL hash'i temizle
            window.location.hash = '';
            
            // Sayfanın en üstüne git
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Layout toggle functions - sadece ürünler için
        function toggleLayout(layoutType) {
            const productGrids = document.querySelectorAll('.products-grid:not(#categoryGrid .products-grid)');
            const toggleButtons = document.querySelectorAll('.layout-toggle-btn');
            
            // Remove active class from all buttons
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            
            productGrids.forEach(grid => {
                if (layoutType === 'grid') {
                    grid.classList.remove('list-view');
                    grid.classList.add('grid-view');
                } else {
                    grid.classList.remove('grid-view');
                    grid.classList.add('list-view');
                }
            });
            
            document.querySelector('[data-layout="' + layoutType + '"]').classList.add('active');
            localStorage.setItem('qrmenu_layout', layoutType);
        }
        
        // Load saved layout
        function loadSavedLayout() {
            const savedLayout = localStorage.getItem('qrmenu_layout') || 'grid';
            toggleLayout(savedLayout);
        }
        


        // Kategori navigasyon fonksiyonları
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved layout
            loadSavedLayout();
            
            // Layout toggle buttons event listeners
            document.querySelectorAll('.layout-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const layout = this.dataset.layout;
                    toggleLayout(layout);
                });
            });
            

            
            // Kategori navigasyon item'larına click event ekle
            document.querySelectorAll('.category-nav-item').forEach(item => {
                item.addEventListener('click', function() {
                    const category = this.dataset.category;
                    
                    // Tıklama ile active class'ı verme, sadece scroll ile belirlensin
                    // Aktif kategoriyi güncelle
                    document.querySelectorAll('.category-nav-item').forEach(navItem => {
                        navItem.classList.remove('active');
                    });
                    
                    // Scroll ile aktif kategori belirleme fonksiyonunu geçici olarak devre dışı bırak
                    window.removeEventListener('scroll', requestTick);
                    
                    // 1 saniye sonra scroll event listener'ı tekrar ekle
                    setTimeout(() => {
                        window.addEventListener('scroll', requestTick);
                    }, 1000);
                    
                    if (category === 'all') {
                        // Tümü seçilirse tüm kategorileri göster
                        showAllCategories();
                    } else {
                        // Belirli kategori seçilirse o kategoriye git
                        showCategory(category);
                        
                        // Kategorinin bulunduğu yere scroll et
                        const categoryElement = document.getElementById('category-' + category);
                        if (categoryElement) {
                            const offset = 100; // Category nav menu yüksekliği
                            const elementPosition = categoryElement.offsetTop;
                            const offsetPosition = elementPosition - offset;
                            
                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
            
                    // Scroll ile aktif kategoriyi belirle
        let ticking = false;
        
        function updateActiveCategory() {
            const scrollPosition = window.scrollY + 150; // Category nav offset - biraz daha yukarıdan başla
            const categories = document.querySelectorAll('.category-section');
            let activeCategory = 'all';
            
            categories.forEach(category => {
                const categoryTop = category.offsetTop;
                const categoryBottom = categoryTop + category.offsetHeight;
                
                if (scrollPosition >= categoryTop && scrollPosition < categoryBottom) {
                    activeCategory = category.id.replace('category-', '');
                }
            });
            
            // Aktif kategoriyi güncelle - tüm aktif class'ları kaldır
            document.querySelectorAll('.category-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            const activeNavItem = document.querySelector(`[data-category="${activeCategory}"]`);
            if (activeNavItem) {
                activeNavItem.classList.add('active');
                
                                    // Aktif kategoriyi görünür alana getir (sadece kategori nav menüsünde)
                    const categoryNavMenu = document.querySelector('.category-nav-menu');
                    if (categoryNavMenu) {
                        const navContainer = categoryNavMenu.querySelector('.category-nav-container');
                        const itemRect = activeNavItem.getBoundingClientRect();
                        const containerRect = navContainer.getBoundingClientRect();
                        
                        if (itemRect.left < containerRect.left || itemRect.right > containerRect.right) {
                            navContainer.scrollTo({
                                left: activeNavItem.offsetLeft - (navContainer.offsetWidth / 2) + (activeNavItem.offsetWidth / 2),
                                behavior: 'smooth'
                            });
                        }
                    }
            }
            
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateActiveCategory);
                ticking = true;
            }
        }
        
        window.addEventListener('scroll', requestTick);
        
        // Masaüstü ve tablette header'ı kaydırma sırasında gizle
        if (window.innerWidth >= 768) {
            let lastScrollTop = 0;
            const header = document.querySelector('.mobile-screen .restaurant-header');
            const mobileScreen = document.querySelector('.mobile-screen');
            
            mobileScreen.addEventListener('scroll', function() {
                const scrollTop = mobileScreen.scrollTop;
                
                if (scrollTop > lastScrollTop && scrollTop > 50) {
                    // Aşağı kaydırma - header'ı gizle
                    header.classList.add('header-hidden');
                } else if (scrollTop < lastScrollTop) {
                    // Yukarı kaydırma - header'ı göster
                    header.classList.remove('header-hidden');
                }
                
                lastScrollTop = scrollTop;
            });
        }
        });

        // Review form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = document.getElementById('selectedRating').value;
            if (rating == 0) {
                alert('Lütfen bir puan seçin!');
                return;
            }
            
            // Form verilerini gönder
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Değerlendirmeniz için teşekkürler!');
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                    // Formu temizle
                    this.reset();
                    document.getElementById('selectedRating').value = 0;
                    document.querySelectorAll('.star-rating').forEach(star => {
                        star.classList.remove('active');
                    });
                } else {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            });
        });
        
        // Modaldan kategori seçimi
        function scrollToCategoryFromModal(categorySlug) {
            // Modalı kapat
            bootstrap.Modal.getInstance(document.getElementById('categoriesModal')).hide();
            
            // Kategori gridini gizle, menüyü göster
            document.getElementById('categoryGrid').style.display = 'none';
            document.getElementById('menuContent').style.display = 'block';
            document.getElementById('backToCategoriesBtn').style.display = 'block';
            
            // Tüm kategorileri göster
            document.querySelectorAll('.category-section').forEach(section => {
                section.style.display = 'block';
            });
            
            // Kategori navigasyonunda ilgili kategoriyi aktif yap
            document.querySelectorAll('.category-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            const categoryNavItem = document.querySelector(`[data-category="${categorySlug}"]`);
            if (categoryNavItem) {
                categoryNavItem.classList.add('active');
            }
            
            // Kategorinin bulunduğu yere scroll et
            setTimeout(() => {
                const categoryElement = document.getElementById('category-' + categorySlug);
                if (categoryElement) {
                    const offset = 120;
                    const elementPosition = categoryElement.offsetTop;
                    const offsetPosition = elementPosition - offset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }, 300);
        }
    </script>
        </div>
    </div>
</body>
</html>