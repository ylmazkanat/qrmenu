@extends('layouts.restaurant')

@section('title', 'Garson Paneli - QR Menu')
@section('page-title', 'Garson Paneli')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-person-badge me-2"></i>
                        Sipariş Alma
                    </h5>
                </div>
                <div class="card-body">
                    <form id="orderForm" action="{{ route('restaurant.orders.create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="table_number" class="form-label">Masa Numarası</label>
                                <input type="text" class="form-control table-number-input" 
                                       id="table_number" name="table_number" required 
                                       placeholder="Masa No" autocomplete="off">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Müşteri Adı (Opsiyonel)</label>
                                <input type="text" class="form-control" 
                                       id="customer_name" name="customer_name" 
                                       placeholder="Müşteri adı">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategoriler ve Ürünler -->
    <div class="row">
        <div class="col-lg-8">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">Menü</h5>
                </div>
                <div class="card-body">
                    <!-- Arama & Kategori Filtreleri -->
                    <div class="mb-3">
                        <input type="text" id="productSearch" class="form-control" placeholder="Ürün ara..." autocomplete="off">
                    </div>
                    <!-- Kategori Filtreleri -->
                    <div class="mb-4">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary category-filter active" data-category="all">
                                Tümü
                            </button>
                            @foreach($categories as $category)
                                <button class="btn btn-outline-primary category-filter" data-category="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Ürün Listesi -->
                    <div class="row" id="productList">
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 mb-3 product-item" data-category="{{ $product->category_id }}" data-product-name="{{ Str::lower($product->name) }} {{ Str::lower($product->description) }}">
                                <div class="product-card" onclick="addToOrder({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             class="card-img-top" style="height: 120px; object-fit: cover;">
                                    @endif
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2">{{ $product->name }}</h6>
                                        <p class="card-text text-muted small">{{ Str::limit($product->description, 60) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-primary">₺{{ number_format($product->price, 2) }}</span>
                                            @if($product->is_available)
                                                <span class="badge bg-success">Mevcut</span>
                                            @else
                                                <span class="badge bg-danger">Tükendi</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sipariş Sepeti -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-cart me-2"></i>
                        Sipariş Sepeti
                    </h5>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-cart-x fs-1"></i>
                            <p class="mt-2">Sepet boş</p>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 mt-3" id="orderSummary" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Toplam:</strong>
                            <strong class="text-primary" id="totalAmount">₺0.00</strong>
                        </div>
                        <button type="button" class="btn btn-restaurant-modern w-100" onclick="submitOrder()">
                            <i class="bi bi-check-circle"></i>
                            Siparişi Gönder
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="clearOrder()">
                            <i class="bi bi-trash"></i>
                            Sepeti Temizle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hazır Siparişler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-bell me-2"></i>
                        Hazır Siparişler
                        <span class="badge bg-success ms-2" id="readyOrderCount">{{ $readyOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div id="readyOrdersList">
                        @forelse($readyOrders as $order)
                            <div class="order-card ready">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                        <small class="text-muted">
                                            {{ $order->created_at->format('H:i') }} - 
                                            ₺{{ number_format($order->total, 2) }}
                                        </small>
                                        <div class="mt-2">
                                            @foreach($order->orderItems as $item)
                                                <span class="badge bg-light text-dark me-1">
                                                    {{ $item->quantity }}x {{ $item->product->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <span class="status-badge status-ready">Hazır</span>
                                        <button class="btn btn-sm btn-success mt-2" 
                                                onclick="markAsDelivered({{ $order->id }})">
                                            <i class="bi bi-check"></i>
                                            Teslim Et
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-clock-history fs-1"></i>
                                <p class="mt-2">Hazır sipariş bulunmuyor</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastAdded" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Ürün sepete eklendi
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let orderItems = [];
    let currentOrderTotal = 0;
    const toastAdded = new bootstrap.Toast(document.getElementById('toastAdded'));

    function showAddedToast() {
        toastAdded.show();
    }

    // Kategori filtreleme
    document.querySelectorAll('.category-filter').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Aktif buton güncelle
            document.querySelectorAll('.category-filter').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Ürünleri filtrele
            document.querySelectorAll('.product-item').forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Ürün arama
    const searchInput = document.getElementById('productSearch');
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const items = document.querySelectorAll('.product-item');
        items.forEach(item => {
            const match = item.dataset.productName.includes(query);
            if (query === '' || match) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Sepete ürün ekleme
    function addToOrder(productId, productName, price) {
        const existingItem = orderItems.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            orderItems.push({
                id: productId,
                name: productName,
                price: price,
                quantity: 1
            });
        }
        
        updateOrderDisplay();
        showAddedToast();
    }

    // Sepetten ürün çıkarma
    function removeFromOrder(productId) {
        orderItems = orderItems.filter(item => item.id !== productId);
        updateOrderDisplay();
    }

    // Ürün miktarını güncelleme
    function updateQuantity(productId, change) {
        const item = orderItems.find(item => item.id === productId);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromOrder(productId);
            } else {
                updateOrderDisplay();
            }
        }
    }

    // Sepet görünümünü güncelleme
    function updateOrderDisplay() {
        const orderItemsContainer = document.getElementById('orderItems');
        const orderSummary = document.getElementById('orderSummary');
        const totalAmountElement = document.getElementById('totalAmount');
        
        if (orderItems.length === 0) {
            orderItemsContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x fs-1"></i>
                    <p class="mt-2">Sepet boş</p>
                </div>
            `;
            orderSummary.style.display = 'none';
            return;
        }
        
        let itemsHtml = '';
        currentOrderTotal = 0;
        
        orderItems.forEach(item => {
            const itemTotal = item.price * item.quantity;
            currentOrderTotal += itemTotal;
            
            itemsHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <div>
                        <div class="fw-medium">${item.name}</div>
                        <small class="text-muted">₺${item.price.toFixed(2)}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span class="fw-bold">${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromOrder(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        orderItemsContainer.innerHTML = itemsHtml;
        totalAmountElement.textContent = `₺${currentOrderTotal.toFixed(2)}`;
        orderSummary.style.display = 'block';
    }

    // Sepeti temizle
    function clearOrder() {
        orderItems = [];
        updateOrderDisplay();
    }

    // Siparişi gönder
    function submitOrder() {
        const tableNumber = document.getElementById('table_number').value;
        const customerName = document.getElementById('customer_name').value;
        
        if (!tableNumber) {
            alert('Lütfen masa numarasını girin!');
            return;
        }
        
        if (orderItems.length === 0) {
            alert('Lütfen sepete ürün ekleyin!');
            return;
        }
        
        const orderData = {
            table_number: tableNumber,
            customer_name: customerName,
            items: orderItems.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                note: item.note || null
            })),
            total: currentOrderTotal,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        fetch('{{ route("restaurant.orders.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
                ,'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sipariş başarıyla oluşturuldu!');
                clearOrder();
                document.getElementById('table_number').value = '';
                document.getElementById('customer_name').value = '';
                location.reload(); // Hazır siparişleri güncellemek için
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Sipariş gönderilirken hata oluştu!');
        });
    }

    // Siparişi teslim et
    function markAsDelivered(orderId) {
        if (confirm('Bu siparişi teslim edildi olarak işaretlemek istediğinizden emin misiniz?')) {
            fetch(`{{ route("restaurant.orders.deliver", ":id") }}`.replace(':id', orderId), {
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

    // Masa numarası input'una otofocus
    document.getElementById('table_number').focus();
</script>
@endsection 