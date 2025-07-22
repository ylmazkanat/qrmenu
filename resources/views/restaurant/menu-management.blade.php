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
                                        <img src="{{ Storage::url($category->image) }}" class="card-img-top" style="height: 150px; object-fit: contain; background: #f8f9fa;">
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
                                            <img src="{{ Storage::url($product->image) }}" style="width: 60px; height: 60px; object-fit: contain; border-radius: 6px; background: #f8f9fa;">
                                        @else
                                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                                <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                    <td>₺{{ number_format($product->price,2) }}</td>
                                    <td>{{ $product->stock }}</td>
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
                                <input type="number" class="form-control" id="productStock" name="stock" min="0" value="100">
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

@section('scripts')
@parent
<script>
let categoryEditMode = false;
let productEditMode = false;

// Kategori işlemleri
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const categoryId = document.getElementById('categoryId').value;
    
    // Form verilerini FormData'ya ekle
    formData.append('name', document.getElementById('categoryName').value);
    formData.append('sort_order', document.getElementById('categorySortOrder').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Resim dosyasını ekle
    const imageFile = document.getElementById('categoryImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    let url = '{{ route("restaurant.categories.store") }}';
    
    if (categoryEditMode && categoryId) {
        url = `{{ url('restaurant/categories') }}/${categoryId}`;
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
</script>
@endsection
@endsection 