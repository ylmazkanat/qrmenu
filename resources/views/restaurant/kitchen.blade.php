@extends('layouts.restaurant')

@section('title', 'Mutfak Paneli - QR Menu')
@section('page-title', 'Mutfak Paneli')

@section('content')
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-value">{{ $pendingOrders->count() }}</div>
                <div class="stats-label">Bekleyen Siparişler</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Yeni gelen
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-fire"></i>
                </div>
                <div class="stats-value">{{ $preparingOrders->count() }}</div>
                <div class="stats-label">Hazırlanıyor</div>
                <div class="stats-change positive">
                    <i class="bi bi-play"></i>
                    Devam eden
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-value">{{ $readyOrders->count() }}</div>
                <div class="stats-label">Hazır</div>
                <div class="stats-change positive">
                    <i class="bi bi-check"></i>
                    Teslim bekliyor
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="stats-value">{{ $todayOrdersCount }}</div>
                <div class="stats-label">Bugün Toplam</div>
                <div class="stats-change positive">
                    <i class="bi bi-calendar-day"></i>
                    Günlük
                </div>
            </div>
        </div>
    </div>

    <!-- Bekleyen Siparişler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock me-2 text-warning"></i>
                        Bekleyen Siparişler
                        <span class="badge bg-warning ms-2">{{ $pendingOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="pendingOrdersList">
                        @forelse($pendingOrders as $order)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="order-card pending">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                            @if($order->customer_name)
                                                <div class="text-primary fw-medium mb-1">{{ $order->customer_name }}</div>
                                            @endif
                                            <small class="text-muted">
                                                Başlama: {{ $order->updated_at->format('H:i') }}
                                                <br>
                                                Süre: {{ $order->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="status-badge status-pending">Bekliyor</span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-items mb-3">
                                        @foreach($order->orderItems as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <div class="fw-medium">{{ $item->product ? $item->product->name : 'Silinmiş Ürün' }}</div>
                                                    @if($item->note)
                                                        <small class="text-muted">Not: {{ $item->note }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge bg-primary">{{ $item->quantity }}x</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center">
                                        <button class="btn btn-restaurant-modern" 
                                                onclick="startPreparing({{ $order->id }})">
                                            <i class="bi bi-play-fill"></i>
                                            Hazırlamaya Başla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-check-circle fs-1"></i>
                                    <h5 class="mt-3">Tüm siparişler işlemde!</h5>
                                    <p>Yeni sipariş bekliyor...</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hazırlanıyor -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-fire me-2 text-info"></i>
                        Hazırlanıyor
                        <span class="badge bg-info ms-2">{{ $preparingOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="preparingOrdersList">
                        @forelse($preparingOrders as $order)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="order-card preparing">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                            <small class="text-muted">
                                                Başlama: {{ $order->updated_at->format('H:i') }}
                                                <br>
                                                Süre: {{ $order->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="status-badge status-preparing">Hazırlanıyor</span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-items mb-3">
                                        @foreach($order->orderItems as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <div class="fw-medium">{{ $item->product ? $item->product->name : 'Silinmiş Ürün' }}</div>
                                                    @if($item->note)
                                                        <small class="text-muted">Not: {{ $item->note }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge bg-info">{{ $item->quantity }}x</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center">
                                        <button class="btn btn-success" 
                                                onclick="markAsReady({{ $order->id }})">
                                            <i class="bi bi-check-circle"></i>
                                            Hazır
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-info-circle fs-3"></i>
                                    <p class="mt-2">Şu anda hazırlanan sipariş yok</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hazır Siparişler -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-check-circle me-2 text-success"></i>
                        Hazır Siparişler (Teslim Bekliyor)
                        <span class="badge bg-success ms-2">{{ $readyOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="readyOrdersList">
                        @forelse($readyOrders as $order)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="order-card ready">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                            @if($order->customer_name)
                                                <div class="text-primary fw-medium mb-1">{{ $order->customer_name }}</div>
                                            @endif
                                            <small class="text-muted">
                                                Hazır: {{ $order->updated_at->format('H:i') }}
                                                <br>
                                                Bekliyor: {{ $order->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="status-badge status-ready">Hazır</span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-items mb-3">
                                        @foreach($order->orderItems as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <div class="fw-medium">{{ $item->product ? $item->product->name : 'Silinmiş Ürün' }}</div>
                                                    @if($item->note)
                                                        <small class="text-muted">Not: {{ $item->note }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge bg-success">{{ $item->quantity }}x</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="badge bg-success p-2 w-100">
                                            <i class="bi bi-bell"></i>
                                            Garson çağrılsın
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-check-all fs-3"></i>
                                    <p class="mt-2">Teslim bekleyen sipariş yok</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Müşteri Tarafından İptal Edilenler -->
    @php
        $customerCancelledOrders = isset($cancelledOrders) ? $cancelledOrders->filter(function($order) {
            return $order->status === 'musteri_iptal';
        }) : collect();
    @endphp
    <div class="row mt-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-x-circle me-2 text-danger"></i>
                        Müşteri Tarafından İptal Edilenler
                        <span class="badge bg-danger ms-2">{{ $customerCancelledOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($customerCancelledOrders as $order)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="order-card cancelled">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                            @if($order->customer_name)
                                                <div class="text-primary fw-medium mb-1">{{ $order->customer_name }}</div>
                                            @endif
                                            <small class="text-muted">İptal Edildi: {{ $order->updated_at->format('H:i') }}</small>
                                            <br>
                                            <span class="badge bg-secondary">Aşama: {{ $order->last_status ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="order-items mb-2">
                                        @foreach($order->orderItems as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <div class="fw-medium">{{ $item->product->name }}</div>
                                                    @if($item->note)
                                                        <small class="text-muted">Not: {{ $item->note }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge bg-danger">{{ $item->quantity }}x</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-danger w-50" onclick="markCancelled({{ $order->id }})">İptal</button>
                                        <button class="btn btn-outline-warning w-50" onclick="markZafiyat({{ $order->id }})">Zafiyat</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-x-circle fs-3"></i>
                                    <p class="mt-2">Müşteri tarafından iptal edilen sipariş yok</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Siparişi hazırlamaya başla
    function startPreparing(orderId) {
        fetch(`{{ route("restaurant.kitchen.start-preparing", ":id") }}`.replace(':id', orderId), {
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
                showSuccessMessage('Sipariş hazırlanmaya başlandı!');
                location.reload();
            } else {
                showErrorMessage('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('İşlem sırasında hata oluştu!');
        });
    }

    // Siparişi hazır olarak işaretle
    function markAsReady(orderId) {
        fetch(`{{ route("restaurant.kitchen.mark-ready", ":id") }}`.replace(':id', orderId), {
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
                showSuccessMessage('Sipariş hazır olarak işaretlendi! Garson bilgilendirildi.');
                location.reload();
            } else {
                showErrorMessage('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('İşlem sırasında hata oluştu!');
        });
    }

    // Başarı mesajı göster
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
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Hata mesajı göster
    function showErrorMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Ses efekti için (yeni sipariş geldiğinde)
    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmgfBj2V4vLGdSgGKnvM8NeFOQgVZLTl5qNOGYM6AAABAAoAAg==');
        audio.play().catch(() => {
            // Ses çalınamazsa sessizce devam et
        });
    }

    // Yeni sipariş kontrol et
    let lastOrderCount = {{ $pendingOrders->count() }};
    function checkNewOrders() {
        fetch('{{ route("restaurant.api.orders.updates") }}')
            .then(response => response.json())
            .then(data => {
                if (data.pending_orders > lastOrderCount) {
                    playNotificationSound();
                    showSuccessMessage(`${data.pending_orders - lastOrderCount} yeni sipariş geldi!`);
                    
                    // Sayfa yenile (3 saniye sonra)
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                lastOrderCount = data.pending_orders;
            })
            .catch(error => {
                console.error('Sipariş kontrol hatası:', error);
            });
    }

    // Her 10 saniyede bir kontrol et
    setInterval(checkNewOrders, 10000);
    
    // Sayfa yüklendiğinde bir kez kontrol et
    setTimeout(checkNewOrders, 2000);

    // Otomatik yenileme (2 dakikada bir - yedek olarak)
    setInterval(() => {
        location.reload();
    }, 120000);

    function markZafiyat(orderId) {
        if (!confirm('Bu siparişi zafiyat olarak işaretlemek istiyor musunuz?')) return;
        fetch(`/restaurant/kitchen/${orderId}/zafiyat`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Sipariş zafiyat olarak işaretlendi.');
                location.reload();
            } else {
                alert(data.message || 'İşlem başarısız.');
            }
        });
    }
    function markCancelled(orderId) {
        if (!confirm('Bu siparişi tamamen iptal etmek istiyor musunuz?')) return;
        fetch(`/restaurant/kitchen/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Sipariş iptal edildi.');
                location.reload();
            } else {
                alert(data.message || 'İşlem başarısız.');
            }
        });
    }
</script>
@endsection 