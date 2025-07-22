@extends('layouts.restaurant')

@section('title', 'Kasiyer Paneli - QR Menu')
@section('page-title', 'Kasiyer Paneli')

@section('content')
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-success text-white">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stats-value">₺{{ number_format($todayRevenue, 2) }}</div>
                <div class="stats-label">Bugün Ciro</div>
                <div class="stats-change positive">
                    <i class="bi bi-graph-up"></i>
                    Günlük gelir
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-info text-white">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stats-value">{{ $todayOrdersCount }}</div>
                <div class="stats-label">Bugün Sipariş</div>
                <div class="stats-change positive">
                    <i class="bi bi-arrow-up"></i>
                    Toplam adet
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-warning text-white">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-value">{{ $paymentPendingOrders->count() }}</div>
                <div class="stats-label">Ödeme Bekliyor</div>
                <div class="stats-change positive">
                    <i class="bi bi-credit-card"></i>
                    Bekleyen
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon gradient-primary text-white">
                    <i class="bi bi-check-all"></i>
                </div>
                <div class="stats-value">{{ $completedOrdersCount }}</div>
                <div class="stats-label">Tamamlanan</div>
                <div class="stats-change positive">
                    <i class="bi bi-check"></i>
                    Bugün
                </div>
            </div>
        </div>
    </div>

    <!-- Ödeme Bekleyen Siparişler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-credit-card me-2 text-warning"></i>
                        Ödeme Bekleyen Siparişler
                        <span class="badge bg-warning ms-2">{{ $paymentPendingOrders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="paymentPendingList">
                        @forelse($paymentPendingOrders as $order)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="order-card ready border-warning">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">Masa {{ $order->table_number }}</h6>
                                            <small class="text-muted">
                                                Hazır: {{ $order->updated_at->format('H:i') }}
                                                <br>
                                                Bekliyor: {{ $order->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="status-badge bg-warning text-dark">Ödeme Bekliyor</span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-items mb-3">
                                        @foreach($order->orderItems as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                <div>
                                                    <div class="fw-medium">{{ $item->product->name }}</div>
                                                    <small class="text-muted">₺{{ number_format($item->price, 2) }}</small>
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge bg-warning text-dark">{{ $item->quantity }}x</span>
                                                    <br>
                                                    <small class="fw-bold">₺{{ number_format($item->price * $item->quantity, 2) }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="border-top pt-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Toplam:</strong>
                                            <strong class="text-primary fs-5">₺{{ number_format($order->total, 2) }}</strong>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success" onclick="showPaymentModal({{ $order->id }}, {{ $order->total }}, '{{ $order->table_number }}')">
                                            <i class="bi bi-credit-card"></i>
                                            Ödeme Al
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="printReceipt({{ $order->id }})">
                                            <i class="bi bi-printer"></i>
                                            Fiş Yazdır
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-check-circle fs-1"></i>
                                    <h5 class="mt-3">Tüm ödemeler tamamlandı!</h5>
                                    <p>Ödeme bekleyen sipariş yok.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Ödemeler -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Son Ödemeler (Bugün)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Masa</th>
                                    <th>Sipariş Saati</th>
                                    <th>Ödeme Saati</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Tutar</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $order)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">Masa {{ $order->table_number }}</span>
                                        </td>
                                        <td>{{ $order->created_at->format('H:i') }}</td>
                                        <td>{{ $order->updated_at->format('H:i') }}</td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $order->payment_method ?? 'Nakit' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">₺{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" onclick="printReceipt({{ $order->id }})">
                                                <i class="bi bi-printer"></i>
                                                Fiş
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Bugün henüz ödeme alınmamış.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Ödeme Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card me-2"></i>
                    Ödeme Al
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h4>Masa <span id="modalTableNumber"></span></h4>
                    <h2 class="text-primary">₺<span id="modalAmount"></span></h2>
                </div>
                
                <form id="paymentForm">
                    <div class="mb-3">
                        <label class="form-label">Ödeme Yöntemi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="cash" value="nakit" checked>
                            <label class="btn btn-outline-success" for="cash">
                                <i class="bi bi-cash"></i>
                                Nakit
                            </label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="card" value="kart">
                            <label class="btn btn-outline-primary" for="card">
                                <i class="bi bi-credit-card"></i>
                                Kart
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="cashFields">
                        <label for="cashReceived" class="form-label">Alınan Tutar</label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" class="form-control" id="cashReceived" 
                                   step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Para üstü: </small>
                            <span class="fw-bold text-success" id="changeAmount">₺0.00</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-success" onclick="processPayment()">
                    <i class="bi bi-check-circle"></i>
                    Ödemeyi Tamamla
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    let currentOrderId = null;
    let currentOrderTotal = 0;

    // Ödeme modalını göster
    function showPaymentModal(orderId, total, tableNumber) {
        currentOrderId = orderId;
        currentOrderTotal = total;
        
        document.getElementById('modalTableNumber').textContent = tableNumber;
        document.getElementById('modalAmount').textContent = total.toFixed(2);
        document.getElementById('cashReceived').value = total.toFixed(2);
        
        updateChangeAmount();
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    // Para üstünü hesapla
    function updateChangeAmount() {
        const received = parseFloat(document.getElementById('cashReceived').value) || 0;
        const change = received - currentOrderTotal;
        const changeElement = document.getElementById('changeAmount');
        
        if (change >= 0) {
            changeElement.textContent = `₺${change.toFixed(2)}`;
            changeElement.className = 'fw-bold text-success';
        } else {
            changeElement.textContent = `₺${Math.abs(change).toFixed(2)} eksik`;
            changeElement.className = 'fw-bold text-danger';
        }
    }

    // Nakit/kart seçimine göre alanları göster/gizle
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cashFields = document.getElementById('cashFields');
            if (this.value === 'nakit') {
                cashFields.style.display = 'block';
                document.getElementById('cashReceived').value = currentOrderTotal.toFixed(2);
                updateChangeAmount();
            } else {
                cashFields.style.display = 'none';
            }
        });
    });

    // Alınan tutar değiştiğinde para üstünü güncelle
    document.getElementById('cashReceived').addEventListener('input', updateChangeAmount);

    // Ödemeyi işle
    function processPayment() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
        
        if (paymentMethod === 'nakit' && cashReceived < currentOrderTotal) {
            alert('Alınan tutar yetersiz!');
            return;
        }
        
        const paymentData = {
            payment_method: paymentMethod,
            cash_received: paymentMethod === 'nakit' ? cashReceived : currentOrderTotal,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        fetch(`{{ route("restaurant.cashier.process-payment", ":id") }}`.replace(':id', currentOrderId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Ödeme başarıyla alındı!');
                
                // Para üstü varsa göster
                if (paymentMethod === 'nakit') {
                    const change = cashReceived - currentOrderTotal;
                    if (change > 0) {
                        showSuccessMessage(`Para üstü: ₺${change.toFixed(2)}`);
                    }
                }
                
                // Modal'ı kapat ve sayfayı yenile
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                location.reload();
            } else {
                showErrorMessage('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Ödeme işlemi sırasında hata oluştu!');
        });
    }

    // Fiş yazdır
    function printReceipt(orderId) {
        const printWindow = window.open(`{{ route("restaurant.cashier.print-receipt", ":id") }}`.replace(':id', orderId), '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
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
</script>
@endsection 