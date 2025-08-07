@extends('layouts.restaurant')

@section('title', 'Garson Paneli - QR Menu')
@section('page-title', 'Garson Paneli')

@section('content')
    <!-- Ana Layout: Menü Sol, Sipariş İşlemleri Sağ -->
    <div class="row">
        <!-- Sol Taraf - Menü -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-journal-text me-2"></i>
                        Menü
                    </h5>
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
                    <div class="row" id="productList" style="max-height: 70vh; overflow-y: auto; padding-right: 10px; scroll-behavior: smooth;">
                        <!-- Mobil için özel scrollbar stili -->
                        <style>
                            #productList {
                                scrollbar-width: thin;
                                scrollbar-color: #007bff #f8f9fa;
                            }
                            #productList::-webkit-scrollbar {
                                width: 8px;
                            }
                            #productList::-webkit-scrollbar-track {
                                background: #f8f9fa;
                                border-radius: 4px;
                            }
                            #productList::-webkit-scrollbar-thumb {
                                background: #007bff;
                                border-radius: 4px;
                            }
                            #productList::-webkit-scrollbar-thumb:hover {
                                background: #0056b3;
                            }
                            @media (max-width: 768px) {
                                #productList {
                                    max-height: 60vh;
                                    padding-right: 5px;
                                }
                            }
                        </style>
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 mb-3 product-item" data-category="{{ $product->category_id }}" data-product-name="{{ Str::lower($product->name) }} {{ Str::lower($product->description) }}">
                                <div class="product-card" onclick="addToOrder({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             class="card-img-top" style="height: 120px; object-fit: contain; background: #f8f9fa;">
                                    @else
                                        <div style="height: 120px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                        </div>
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

        <!-- Sağ Taraf - Sipariş Alma ve Sepet -->
        <div class="col-lg-4">
            <!-- Sipariş Alma -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-person-badge me-2"></i>
                        Sipariş Alma
                    </h5>
                </div>
                <div class="card-body">
                    <form id="orderForm" action="{{ route('restaurant.orders.create') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="table_number" class="form-label">Masa Numarası</label>
                            <div class="input-group">
                                <select class="form-control" id="table_number" name="table_number" required>
                                    <option value="">Masa Seçin</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->table_number }}">
                                            Masa {{ $table->table_number }}
                                            @if($table->capacity) - {{ $table->capacity }} kişi @endif
                                            @if($table->location) - {{ $table->location }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickTableModal">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Müşteri Adı</label>
                            <input type="text" class="form-control" 
                                   id="customer_name" name="customer_name" 
                                   placeholder="Müşteri adı (opsiyonel)">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sipariş Sepeti -->
            <div class="content-card sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-cart me-2"></i>
                        Sipariş Sepeti
                        <span class="badge bg-primary ms-2" id="cartItemCount">0</span>
                    </h5>
                    <!-- Ses Kontrolleri -->
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="testNotificationSound()" title="Ses testi">
                            <i class="bi bi-volume-up"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-cart-x fs-1"></i>
                            <p class="mt-2">Sepet boş</p>
                            <small class="text-muted">Ürünlere tıklayarak sepete ekleyin</small>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 mt-3" id="orderSummary" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Toplam:</strong>
                            <strong class="text-primary fs-5" id="totalAmount">₺0.00</strong>
                        </div>
                        <button type="button" class="btn btn-restaurant-modern w-100 btn-lg" onclick="submitOrder()">
                            <i class="bi bi-check-circle me-2"></i>
                            Siparişi Gönder
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="clearOrder()">
                            <i class="bi bi-trash me-2"></i>
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

    <!-- Benim Siparişlerim -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-person-check me-2"></i>
                        Benim Siparişlerim
                        <span class="badge bg-primary ms-2">{{ $myOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($myOrders as $order)
                        <div class="order-card mb-3 border-primary" style="border-left: 4px solid var(--bs-primary);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                    @if($order->customer_name)
                                        <div class="text-primary fw-medium mb-1">{{ $order->customer_name }}</div>
                                    @endif
                                    <small class="text-muted">
                                        {{ $order->created_at->format('H:i') }} - {{ $order->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @elseif($order->status == 'preparing')
                                        <span class="badge bg-info">Hazırlanıyor</span>
                                    @elseif($order->status == 'ready')
                                        <span class="badge bg-success">Hazır</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-primary">Teslim Edildi</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge bg-dark">Tamamlandı</span>
                                    @endif
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editOrder({{ $order->id }}, '{{ $order->table_number }}')">
                                                <i class="bi bi-pencil me-2"></i>Masa Değiştir</a></li>
                                            @if($order->status == 'pending')
                                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder({{ $order->id }})">
                                                    <i class="bi bi-x-circle me-2"></i>İptal Et</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="order-items mb-2">
                                @foreach($order->orderItems as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="fw-medium">{{ $item->product->name }}</span>
                                            @if($item->note)
                                                <br><small class="text-muted">Not: {{ $item->note }}</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">{{ $item->quantity }}x</span>
                                            <br><small class="text-muted">₺{{ number_format($item->price * $item->quantity, 2) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Toplam: ₺{{ number_format($order->total, 2) }}</strong>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-info-circle fs-3"></i>
                            <p class="mt-2">Henüz sipariş oluşturmadınız</p>
                        </div>
                    @endforelse
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
    // --- Sade Bildirim Sistemi (Mutfak paneli gibi) ---
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
        updateCartCounter();
        showAddedToast();
    }

    // Sepetten ürün çıkarma
    function removeFromOrder(productId) {
        orderItems = orderItems.filter(item => item.id !== productId);
        updateOrderDisplay();
        updateCartCounter();
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
                updateCartCounter();
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
        updateCartCounter();
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

    

    // Bildirim göster
    function showNotification(title, message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const newToast = new bootstrap.Toast(document.getElementById(toastId));
        newToast.show();
        
        // Toast kapatıldıktan sonra DOM'dan kaldır
        setTimeout(() => {
            const element = document.getElementById(toastId);
            if (element) element.remove();
        }, 6000);
    }

    // Ses efekti (mutfak paneliyle aynı)
    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmgfBj2V4vLGdSgGKnvM8NeFOQgVZLTl5qNOGYM6AAABAAoAAg==');
        audio.play().catch(() => {});
    }

    // Başarı mesajı göster (mutfak paneliyle aynı)
    function showSuccessMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => { alert.remove(); }, 5000);
    }

    // Hazır sipariş polling
    let lastReadyOrderCount = {{ $readyOrders->count() }};
    function checkReadyOrders() {
        fetch('{{ route("restaurant.api.orders.updates") }}')
            .then(response => response.json())
            .then(data => {
                if (data.ready_orders > lastReadyOrderCount) {
                    playNotificationSound();
                    showSuccessMessage(`${data.ready_orders - lastReadyOrderCount} yeni hazır sipariş var!`);
                    setTimeout(() => { location.reload(); }, 3000);
                }
                lastReadyOrderCount = data.ready_orders;
            })
            .catch(error => { console.error('Sipariş kontrol hatası:', error); });
    }
    setInterval(checkReadyOrders, 10000);
    setTimeout(checkReadyOrders, 2000);

    // Sipariş iptal etme
    function cancelOrder(orderId) {
        if (!confirm('Bu siparişi iptal etmek istediğinizden emin misiniz?')) {
            return;
        }

        fetch(`/restaurant/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Hata oluştu');
            }
        })
        .catch(error => {
            alert('Hata: ' + error.message);
        });
    }

    // Sipariş düzenleme (masa değiştirme)
    function editOrder(orderId, currentTable) {
        const newTable = prompt('Yeni masa numarası:', currentTable);
        if (!newTable || newTable === currentTable) {
            return;
        }

        fetch(`/restaurant/orders/${orderId}/update-table`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                table_number: newTable
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Hata oluştu');
            }
        })
        .catch(error => {
            alert('Hata: ' + error.message);
        });
    }

    // Sayfa yüklendiğinde başlat
    document.addEventListener('DOMContentLoaded', function() {
        // Polling başlat (sayfa görünürlüğüne göre ayarlanacak)
        pollingInterval = setInterval(checkReadyOrders, 8000); // 8 saniye başlangıç
        

        
        // Test bildirimi (sadece geliştirme için)
        if (window.location.hostname === '127.0.0.1') {
            setTimeout(() => {
    
            }, 3000);
        }
    });

    // Sepet sayacını güncelle
    function updateCartCounter() {
        const cartCount = Object.keys(orderItems).length;
        const counter = document.getElementById('cartItemCount');
        if (counter) {
            counter.textContent = cartCount;
            counter.className = cartCount > 0 ? 'badge bg-warning text-dark ms-2' : 'badge bg-primary ms-2';
        }
    }

    // Ses test fonksiyonu
    function testNotificationSound() {
        
        playNotificationSound();
        
        // Test toast göster
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show border-0 bg-info text-white" role="alert">
                <div class="toast-header bg-info text-white border-0">
                    <i class="bi bi-volume-up me-2"></i>
                    <strong class="me-auto">Ses Testi</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    🔊 Sesli bildirim test edildi! Ses duydunuz mu?
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Ses ön yükleme ve hazırlama
    function preloadAndTestAudio() {
        if (notificationAudio) {
            // Ses dosyasını ön yükle
            notificationAudio.load();
            
            // İlk kullanıcı etkileşiminde test et
            document.addEventListener('click', function testAudioOnce() {
                setTimeout(() => {
            
                    
                    // Sessiz test (volume 0)
                    const originalVolume = notificationAudio.volume;
                    notificationAudio.volume = 0;
                    notificationAudio.currentTime = 0;
                    
                    const testPromise = notificationAudio.play();
                    if (testPromise !== undefined) {
                        testPromise
                            .then(() => {
                                notificationAudio.pause();
                                notificationAudio.volume = originalVolume;
                        
                            })
                            .catch((error) => {
                                notificationAudio.volume = originalVolume;
                                console.warn('⚠️ Ses testi başarısız:', error);
                            });
                    }
                }, 100);
                
                document.removeEventListener('click', testAudioOnce);
            }, { once: true });
        }
    }
</script>
<!-- Hızlı Masa Ekleme Modal -->
<div class="modal fade" id="quickTableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hızlı Masa Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickTableForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickTableNumber" class="form-label">Masa Numarası</label>
                                <input type="text" class="form-control" id="quickTableNumber" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickTableCapacity" class="form-label">Kapasite</label>
                                <input type="number" class="form-control" id="quickTableCapacity" min="1" max="20">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="quickTableLocation" class="form-label">Konum</label>
                        <input type="text" class="form-control" id="quickTableLocation" placeholder="Örn: Teras, İç Salon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Masa Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hızlı masa ekleme (geçici - veritabanına kaydetmez)
document.getElementById('quickTableForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tableNumber = document.getElementById('quickTableNumber').value.trim();
    
    if (!tableNumber) {
        alert('Masa numarası gereklidir!');
        return;
    }
    
         // Selectbox'a geçici masa ekle
     const tableSelect = document.getElementById('table_number');
    
    // Zaten var mı kontrol et
    const existingOption = Array.from(tableSelect.options).find(option => option.value === tableNumber);
    if (existingOption) {
        alert('Bu masa numarası zaten mevcut!');
        return;
    }
    
    // Yeni option ekle (geçici)
    const newOption = document.createElement('option');
    newOption.value = tableNumber;
    newOption.textContent = `Masa ${tableNumber} (Geçici)`;
    newOption.setAttribute('data-temp', 'true');
    tableSelect.appendChild(newOption);
    
    // Yeni masayı seç
    tableSelect.value = tableNumber;
    
    // Modal'ı kapat
    bootstrap.Modal.getInstance(document.getElementById('quickTableModal')).hide();
    
    // Form'u temizle
    document.getElementById('quickTableForm').reset();
    
         alert('Geçici masa eklendi! Bu masa sadece sipariş için kullanılacak, veritabanına kaydedilmeyecek.');
            document.getElementById('quickTableForm').reset();
            

});
</script>

@endsection