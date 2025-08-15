@extends('layouts.restaurant')

@section('title', 'Menü Yönetimi - '.$restaurant->name)
@section('page-title', 'Menü Yönetimi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tags me-2"></i>
                    Kategoriler
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-plus-circle me-1"></i>
                    Yeni Kategori
                </button>
            </div>
            <div class="card-body">
                @if($categories->count() > 0)
                    <div class="row" id="categoriesContainer">
                        @foreach($categories as $category)
                            <div class="col-md-6 col-lg-4 mb-4" data-category-id="{{ $category->id }}">
                                <div class="card h-100 shadow-sm border-0">
                                    @if($category->image)
                                        <img src="{{ Storage::url($category->image) }}" class="card-img-top" style="height: 150px; object-fit: cover; background: #f8f9fa;">
                                    @else
                                        <div style="height: 150px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="mb-1">{{ $category->name }}</h6>
                                        <small class="text-muted mb-2">{{ $category->products_count }} ürün</small>
                                        <div class="mt-auto d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', {{ $category->sort_order }})" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5" id="noCategoriesMessage">
                        <i class="bi bi-tag fs-1"></i>
                        <p class="mt-3">Henüz kategori eklenmedi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-basket me-2"></i>
                    Ürünler
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="bi bi-plus-circle me-1"></i>
                    Yeni Ürün
                </button>
            </div>
            <div class="card-body table-responsive">
                @if($products->count() > 0)
                    <table class="table table-bordered align-middle" id="productsTable">
                        <thead>
                            <tr>
                                <th>Görsel</th>
                                <th>Ürün</th>
                                <th>Kategori</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th>Durum</th>
                                <th>Aksiyon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr data-product-id="{{ $product->id }}">
                                    <td style="width: 70px;">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; background: #f8f9fa;">
                                        @else
                                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                                                    <td>₺{{ number_format($product->price,2) }}</td>
                                <td>
                                    {{ $product->stock_display }}
                                    @if($product->stock == -1)
                                        <small class="text-muted">(Sınırsız)</small>
                                    @endif
                                </td>
                                    <td>
                                        <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->is_available ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" onclick="editProduct({{ $product->id }})" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteProduct({{ $product->id }})" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted py-5" id="noProductsMessage">
                        <i class="bi bi-box fs-1"></i>
                        <p class="mt-3">Henüz ürün eklenmedi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Masa Yönetimi -->
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>
                    Masa Yönetimi
                </h5>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#tableModal">
                    <i class="bi bi-plus-circle me-1"></i>
                    Yeni Masa
                </button>
            </div>
            <div class="card-body">
                @if($tables->count() > 0)
                    <div class="row" id="tablesContainer">
                        @foreach($tables as $table)
                            <div class="col-md-6 col-lg-4 mb-4" data-table-id="{{ $table->id }}">
                                <div class="card h-100 shadow-sm border-0">
                                    <div style="height: 120px; background: linear-gradient(135deg, #28a745, #20c997); display: flex; align-items: center; justify-content: center;">
                                        <div class="text-center text-white">
                                            <i class="bi bi-table" style="font-size: 2rem;"></i>
                                            <h4 class="mt-2 mb-0">Masa {{ $table->table_number }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            @if($table->capacity)
                                                <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $table->capacity }} kişi</small><br>
                                            @endif
                                            @if($table->location)
                                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $table->location }}</small><br>
                                            @endif
                                            <span class="badge {{ $table->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $table->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">QR Kodu:</small>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm" value="{{ route('menu.show', $restaurant->slug) }}#masa{{ $table->table_number }}" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ route('menu.show', $restaurant->slug) }}#masa{{ $table->table_number }}')">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" type="button" onclick="showTableQR('{{ $table->table_number }}', '{{ route('menu.show', $restaurant->slug) }}#masa{{ $table->table_number }}')">
                                                    <i class="bi bi-qr-code"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-auto d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editTable({{ $table->id }})" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTable({{ $table->id }})" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5" id="noTablesMessage">
                        <i class="bi bi-table fs-1"></i>
                        <p class="mt-3">Henüz masa eklenmedi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sipariş Ayarları -->
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Sipariş Ayarları
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderSettingsModal">
                    <i class="bi bi-gear me-1"></i>
                    Ayarları Düzenle
                </button>
            </div>
            <div class="card-body">
                @if($restaurant->orderSettings)
                    <div class="mb-3">
                        <strong>Sipariş Durumu:</strong>
                        <span class="badge {{ $restaurant->orderSettings->ordering_enabled ? 'bg-success' : 'bg-danger' }} ms-2">
                            {{ $restaurant->orderSettings->ordering_enabled ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    
                    @if($restaurant->orderSettings->enabled_categories)
                        <div class="mb-3">
                            <strong>Sipariş Alınan Kategoriler:</strong>
                            <div class="mt-2">
                                @if(in_array('all', $restaurant->orderSettings->enabled_categories))
                                    <span class="badge bg-primary me-1">Tüm Kategoriler</span>
                                @else
                                    @foreach($categories as $category)
                                        @if(in_array($category->id, $restaurant->orderSettings->enabled_categories))
                                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Sipariş ayarları henüz yapılandırılmamış. Varsayılan olarak tüm kategorilerden sipariş alınabilir.
                        </div>
                    @endif
                @else
                    <div class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Sipariş ayarları henüz yapılandırılmamış. Ayarları düzenlemek için yukarıdaki butona tıklayın.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Kategori Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Yeni Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoryImage" class="form-label">Kategori Resmi</label>
                        <input type="file" class="form-control" id="categoryImage" name="image" accept="image/*" onchange="previewCategoryImage(this)">
                        <small class="form-text text-muted">JPEG, PNG, JPG, GIF formatları desteklenir. Maksimum 2MB.</small>
                        <div id="categoryImagePreview" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorySortOrder" class="form-label">Sıra</label>
                        <input type="number" class="form-control" id="categorySortOrder" name="sort_order" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ürün Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Yeni Ürün</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="product_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Ürün Adı</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Kategori</label>
                                <select class="form-control" id="productCategory" name="category_id">
                                    <option value="">Kategori Seçin</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Ürün Resmi</label>
                        <input type="file" class="form-control" id="productImage" name="image" accept="image/*" onchange="previewProductImage(this)">
                        <small class="form-text text-muted">JPEG, PNG, JPG, GIF formatları desteklenir. Maksimum 2MB.</small>
                        <div id="productImagePreview" class="mt-2"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Fiyat (₺)</label>
                                <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="productStock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="productStock" name="stock" min="-1" value="100">
                                <small class="text-muted">-1 = Sınırsız stok</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="productSortOrder" class="form-label">Sıra</label>
                                <input type="number" class="form-control" id="productSortOrder" name="sort_order" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="productIsAvailable" name="is_available" checked>
                            <label class="form-check-label" for="productIsAvailable">
                                Ürün Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Masa Modal -->
<div class="modal fade" id="tableModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tableModalLabel">Yeni Masa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="document.getElementById('tableForm').reset();"></button>
            </div>
            <form id="tableForm" onreset="tableEditMode = false;">
                <div class="modal-body">
                    <input type="hidden" id="tableId" name="table_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tableNumber" class="form-label">Masa Numarası</label>
                                <input type="text" class="form-control" id="tableNumber" name="table_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tableCapacity" class="form-label">Kapasite</label>
                                <input type="number" class="form-control" id="tableCapacity" name="capacity" min="1" max="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tableLocation" class="form-label">Konum</label>
                        <input type="text" class="form-control" id="tableLocation" name="location" placeholder="Örn: Balkon, İç Salon, Teras">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tableDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="tableDescription" name="description" rows="2" placeholder="Masa hakkında notlar"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tableIsActive" name="is_active" checked>
                            <label class="form-check-label" for="tableIsActive">
                                Masa Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sipariş Ayarları Modal -->
    <!-- Meta bilgileri -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Sipariş Ayarları Modal -->
    <div class="modal fade" id="orderSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Ayarları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderSettingsForm">
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="orderingEnabled" 
                                   {{ $restaurant->orderSettings && $restaurant->orderSettings->ordering_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="orderingEnabled">
                                <strong>Sipariş Almayı Aktif Et</strong>
                            </label>
                        </div>
                        <small class="text-muted">Bu seçenek kapalıysa müşteriler sipariş veremez.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Sipariş Alınacak Kategoriler</strong></label>
                        <div class="border rounded p-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="allCategories" 
                                       {{ !$restaurant->orderSettings || !$restaurant->orderSettings->enabled_categories || in_array('all', $restaurant->orderSettings->enabled_categories ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-primary" for="allCategories">
                                    Tüm Kategoriler
                                </label>
                            </div>
                            <hr class="my-2">
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox" type="checkbox" 
                                           id="category_{{ $category->id }}" value="{{ $category->id }}"
                                           {{ $restaurant->orderSettings && $restaurant->orderSettings->enabled_categories && in_array($category->id, $restaurant->orderSettings->enabled_categories) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">
                            "Tüm Kategoriler" seçiliyse, menüdeki tüm ürünlerde sepete ekle butonu görünür.<br>
                            Belirli kategoriler seçiliyse, sadece o kategorilerdeki ürünlerde sepete ekle butonu görünür.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
let categoryEditMode = false;
let productEditMode = false;

// Kategori işlemleri
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const categoryId = document.getElementById('categoryId').value;
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    let url = '{{ route("restaurant.categories.store") }}';
    
    if (categoryEditMode && categoryId) {
        url = `{{ url('restaurant/categories') }}/${categoryId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
});

function editCategory(id, name, sortOrder) {
    categoryEditMode = true;
    document.getElementById('categoryModalLabel').textContent = 'Kategori Düzenle';
    document.getElementById('categoryId').value = id;
    
    // Backend'den kategori bilgilerini çek
    fetch(`{{ url('restaurant/categories') }}/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const category = data.category;
            
            // Form alanlarını doldur
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categorySortOrder').value = category.sort_order || 0;
            
            // Mevcut resmi göster
            const preview = document.getElementById('categoryImagePreview');
            preview.innerHTML = '';
            if (category.image) {
                const img = document.createElement('img');
                img.src = `/storage/${category.image}`;
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'contain';
                preview.appendChild(img);
            }
            
            // Modal'ı göster
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        } else {
            alert('Kategori bilgileri alınamadı: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

function deleteCategory(id) {
    if (!confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`{{ url('restaurant/categories') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: '_method=DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

// Ürün işlemleri
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const productId = document.getElementById('productId').value;
    
    // Form verilerini FormData'ya ekle
    formData.append('name', document.getElementById('productName').value);
    formData.append('description', document.getElementById('productDescription').value);
    formData.append('price', document.getElementById('productPrice').value);
    formData.append('category_id', document.getElementById('productCategory').value);
    formData.append('stock', document.getElementById('productStock').value);
    formData.append('sort_order', document.getElementById('productSortOrder').value);
    formData.append('is_available', document.getElementById('productIsAvailable').checked ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Resim dosyasını ekle
    const imageFile = document.getElementById('productImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    let url = '{{ route("restaurant.products.store") }}';
    
    if (productEditMode && productId) {
        url = `{{ url('restaurant/products') }}/${productId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
});

function editProduct(id) {
    productEditMode = true;
    document.getElementById('productModalLabel').textContent = 'Ürün Düzenle';
    document.getElementById('productId').value = id;
    
    // Backend'den ürün bilgilerini çek
    fetch(`{{ url('restaurant/products') }}/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const product = data.product;
            
            // Form alanlarını doldur
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productCategory').value = product.category_id || '';
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productSortOrder').value = product.sort_order;
            document.getElementById('productIsAvailable').checked = product.is_available;
            
            // Mevcut resmi göster
            const preview = document.getElementById('productImagePreview');
            preview.innerHTML = '';
            if (product.image) {
                const img = document.createElement('img');
                img.src = `/storage/${product.image}`;
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'contain';
                preview.appendChild(img);
            }
            
            // Modal'ı göster
            new bootstrap.Modal(document.getElementById('productModal')).show();
        } else {
            alert('Ürün bilgileri alınamadı: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

function deleteProduct(id) {
    if (!confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`{{ url('restaurant/products') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: '_method=DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

// Modal sıfırlama
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
    categoryEditMode = false;
    document.getElementById('categoryModalLabel').textContent = 'Yeni Kategori';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryImagePreview').innerHTML = ''; // Resim önizlemeyi temizle
});

document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
    productEditMode = false;
    document.getElementById('productModalLabel').textContent = 'Yeni Ürün';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productImagePreview').innerHTML = ''; // Resim önizlemeyi temizle
});

// Resim önizleme fonksiyonları
function previewCategoryImage(input) {
    const preview = document.getElementById('categoryImagePreview');
    preview.innerHTML = ''; // Önceki önizlemeyi temizle
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            img.style.objectFit = 'contain';
            preview.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewProductImage(input) {
    const preview = document.getElementById('productImagePreview');
    preview.innerHTML = ''; // Önceki önizlemeyi temizle
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            img.style.objectFit = 'contain';
            preview.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Masa işlemleri
let tableEditMode = false;

document.getElementById('tableForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const tableId = document.getElementById('tableId').value;
    
    // Form verilerini FormData'ya ekle
    formData.append('table_number', document.getElementById('tableNumber').value);
    formData.append('capacity', document.getElementById('tableCapacity').value);
    formData.append('location', document.getElementById('tableLocation').value);
    formData.append('description', document.getElementById('tableDescription').value);
    formData.append('is_active', document.getElementById('tableIsActive').checked ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    
    let url = '{{ route("restaurant.tables.store") }}';
    
    if (tableEditMode && tableId) {
        url = `{{ url('restaurant/tables') }}/${tableId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
});

function editTable(id) {
    tableEditMode = true;
    document.getElementById('tableModalLabel').textContent = 'Masa Düzenle';
    document.getElementById('tableId').value = id;
    
    // Backend'den masa bilgilerini çek
    fetch(`{{ url('restaurant/tables') }}/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const table = data.table;
            
            // Form alanlarını doldur
            document.getElementById('tableNumber').value = table.table_number;
            document.getElementById('tableCapacity').value = table.capacity || '';
            document.getElementById('tableLocation').value = table.location || '';
            document.getElementById('tableDescription').value = table.description || '';
            document.getElementById('tableIsActive').checked = table.is_active;
            
            // Modal'ı göster
            new bootstrap.Modal(document.getElementById('tableModal')).show();
        } else {
            alert('Masa bilgileri alınamadı: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

function deleteTable(id) {
    if (!confirm('Bu masayı silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`{{ url('restaurant/tables') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: '_method=DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
}

// Masa modal sıfırlama
document.getElementById('tableModal').addEventListener('hidden.bs.modal', function () {
    tableEditMode = false;
    document.getElementById('tableModalLabel').textContent = 'Yeni Masa';
    document.getElementById('tableForm').reset();
    document.getElementById('tableId').value = '';
    document.getElementById('tableIsActive').checked = true;
});

// QR link kopyalama fonksiyonu
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('QR linki kopyalandı!');
    }, function(err) {
        console.error('Kopyalama hatası: ', err);
        // Fallback için input elementi oluştur
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('QR linki kopyalandı!');
    });
}

// Sipariş ayarları
document.getElementById('orderSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('ordering_enabled', document.getElementById('orderingEnabled').checked ? '1' : '0');
    
    const allCategories = document.getElementById('allCategories').checked;
    if (allCategories) {
        formData.append('enabled_categories', JSON.stringify(['all']));
    } else {
        const selectedCategories = [];
        document.querySelectorAll('.category-checkbox:checked').forEach(checkbox => {
            selectedCategories.push(parseInt(checkbox.value));
        });
        formData.append('enabled_categories', JSON.stringify(selectedCategories));
    }
    
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("restaurant.order-settings.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    });
});

// Tüm kategoriler checkbox davranışı
document.getElementById('allCategories').addEventListener('change', function() {
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    if (this.checked) {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = true;
        });
    } else {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });
    }
});

// Kategori checkbox'ları değiştiğinde tüm kategoriler'i kapat
document.querySelectorAll('.category-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('allCategories').checked = false;
        }
    });
});

// QR Kod Modal'ı göster - Basit ve güvenilir yöntem
function showTableQR(tableNumber, tableUrl) {
    
    
    try {
        // Modal elementlerini kontrol et
        const qrModal = document.getElementById('qrCodeModal');
        const qrTableSpan = document.getElementById('qrTableNumber');
        const qrContainer = document.getElementById('qrCodeContainer');
        
        if (!qrModal || !qrTableSpan || !qrContainer) {
            alert('QR Modal elementleri bulunamadı');
            return;
        }
        
        // Modal başlığını ayarla
        qrTableSpan.textContent = tableNumber;
        
        // Modal'ı göster
        const modal = new bootstrap.Modal(qrModal);
        modal.show();
        
        // QR kod oluştur - Basit ve garantili yöntem
        generateSimpleQR(qrContainer, tableUrl, tableNumber);
        
    } catch (error) {
        console.error('QR Modal hatası:', error);
        alert('QR kod modalı açılamadı: ' + error.message);
    }
}

// Basit ve güvenilir QR kod oluşturma
function generateSimpleQR(qrContainer, tableUrl, tableNumber) {
    
    
    // Loading göster
    qrContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><br><small class="text-muted mt-2">QR kod oluşturuluyor...</small></div>';
    
    // QR.io API kullanarak basit QR kod oluşturma
    const encodedUrl = encodeURIComponent(tableUrl);
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodedUrl}&margin=10`;
    
    const img = document.createElement('img');
    img.src = qrApiUrl;
    img.alt = `Masa ${tableNumber} QR Kodu`;
    img.style.cssText = 'width: 250px; height: 250px; border: 2px solid #e9ecef; border-radius: 12px; background: white; padding: 10px;';
    img.crossOrigin = 'anonymous';
    
    img.onload = () => {
        
        
        // QR kod konteynerini temizle ve resmi ekle
        qrContainer.innerHTML = '';
        qrContainer.appendChild(img);
        
        // Download butonunu ayarla
        setupSimpleDownload(img, tableNumber);
        
        // QR kod bilgisi ekle
        const infoDiv = document.createElement('div');
        infoDiv.className = 'mt-3 text-center';
        infoDiv.innerHTML = `
            <small class="text-muted d-block">Masa ${tableNumber} için QR Kod</small>
            <small class="text-muted">Müşteriler bu kodu okutarak menüye erişebilir</small>
        `;
        qrContainer.appendChild(infoDiv);
    };
    
    img.onerror = () => {
        console.warn('QRServer API başarısız, alternatif deneniyor...');
        tryAlternativeQR(qrContainer, tableUrl, tableNumber);
    };
}

// Alternatif QR API'leri
function tryAlternativeQR(qrContainer, tableUrl, tableNumber) {
    const encodedUrl = encodeURIComponent(tableUrl);
    const alternativeUrls = [
        `https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=${encodedUrl}&choe=UTF-8`,
        `https://qr-code-generator-api.vercel.app/api/qr?text=${encodedUrl}&size=250`,
        `https://quickchart.io/qr?text=${encodedUrl}&size=250`
    ];
    
    let currentIndex = 0;
    
    function tryNext() {
        if (currentIndex >= alternativeUrls.length) {
            // Tüm API'ler başarısız, manuel link göster
            showManualLink(qrContainer, tableUrl, tableNumber);
            return;
        }
        
        const img = document.createElement('img');
        img.src = alternativeUrls[currentIndex];
        img.alt = `Masa ${tableNumber} QR Kodu`;
        img.style.cssText = 'width: 250px; height: 250px; border: 2px solid #e9ecef; border-radius: 12px; background: white; padding: 10px;';
        img.crossOrigin = 'anonymous';
        
        img.onload = () => {
            
            qrContainer.innerHTML = '';
            qrContainer.appendChild(img);
            setupSimpleDownload(img, tableNumber);
        };
        
        img.onerror = () => {
            console.warn('Alternatif API başarısız:', alternativeUrls[currentIndex]);
            currentIndex++;
            tryNext();
        };
    }
    
    tryNext();
}

// Manuel link gösterme (son çare)
function showManualLink(qrContainer, tableUrl, tableNumber) {
    qrContainer.innerHTML = `
        <div class="text-center p-4">
            <div class="alert alert-warning border-0 shadow-sm">
                <h6 class="alert-heading">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    QR Kod Oluşturulamadı
                </h6>
                <p class="mb-3">Masa ${tableNumber} için doğrudan link:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="${tableUrl}" readonly id="manualUrl${tableNumber}">
                    <button class="btn btn-outline-primary" type="button" onclick="copyManualUrl('${tableNumber}')">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <small class="text-muted">Bu linki müşterilerinizle paylaşabilirsiniz</small>
            </div>
        </div>
    `;
}

// Basit download sistemi
function setupSimpleDownload(img, tableNumber) {
    const downloadBtn = document.getElementById('downloadQRBtn');
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            try {
                // Canvas oluştur ve resmi çiz
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                canvas.width = 300;
                canvas.height = 300;
                
                // Beyaz arkaplan
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, 300, 300);
                
                // QR kodu çiz
                ctx.drawImage(img, 25, 25, 250, 250);
                
                // Download
                const link = document.createElement('a');
                link.download = `masa-${tableNumber}-qr-kod.png`;
                link.href = canvas.toDataURL('image/png');
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
        
                
            } catch (error) {
                console.error('Download hatası:', error);
                alert('QR kod indirilemedi. Lütfen sağ tıklayıp "Resmi farklı kaydet" seçeneğini kullanın.');
            }
        };
    }
}

// Manuel URL kopyalama
function copyManualUrl(tableNumber) {
    const urlInput = document.getElementById(`manualUrl${tableNumber}`);
    if (urlInput) {
        urlInput.select();
        urlInput.setSelectionRange(0, 99999);
        
        // Modern clipboard API
        if (navigator.clipboard) {
            navigator.clipboard.writeText(urlInput.value).then(() => {
                showCopySuccess();
            }).catch(() => {
                fallbackCopy(urlInput);
            });
        } else {
            fallbackCopy(urlInput);
        }
    }
}

// Fallback kopyalama
function fallbackCopy(urlInput) {
    try {
        document.execCommand('copy');
        showCopySuccess();
    } catch (error) {
        alert('Kopyalama başarısız. Lütfen manuel olarak seçip kopyalayın.');
    }
}

// Kopyalama başarı mesajı
function showCopySuccess() {
    // Geçici toast mesajı
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Başarılı</strong>
            </div>
            <div class="toast-body">
                Link kopyalandı!
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

<!-- QR Kod Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-qr-code me-2"></i>
                    Masa <span id="qrTableNumber"></span> QR Kodu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3"></div>
                <p class="text-muted">Müşteriler bu QR kodu okutarak doğrudan masa numarasıyla menüye erişebilir.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="downloadQRBtn">
                    <i class="bi bi-download me-1"></i>
                    QR Kodu İndir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR kod için harici kütüphane gerekmez - API tabanlı sistem -->

@endsection
@endsection