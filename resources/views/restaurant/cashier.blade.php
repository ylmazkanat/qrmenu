@extends('layouts.restaurant')

@section('title', 'Kasiyer Paneli - QR Menu')
@section('page-title', 'Kasiyer Paneli')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-outline-dark" id="endOfDayReportBtn">
            <i class="bi bi-file-earmark-pdf"></i> Gün Sonu (PDF)
        </button>
    </div>
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
                    <i class="bi bi-table"></i>
                </div>
                <div class="stats-value">{{ $openOrders->count() }}</div>
                <div class="stats-label">Açık Masalar</div>
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

    <!-- Masalar (Açık/Kapalı) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-table me-2 text-warning"></i>
                        Masa Durumu
                        <span class="badge bg-warning ms-2">{{ $openOrders->count() }} açık masa</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="tablesGrid">
                        @forelse($openOrders as $tableNumber => $orders)
                            @php
                                $totalAmount = $orders->sum('total');
                                $hasDelivered = $orders->where('status', 'delivered')->count() > 0;
                                $allDelivered = $orders->every(fn($order) => $order->status === 'delivered');
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="table-card {{ $allDelivered ? 'ready-for-payment' : 'has-pending' }}" 
                                     data-table="{{ $tableNumber }}" 
                                     onclick="showTableDetails('{{ $tableNumber }}')">
                                    <div class="table-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Masa {{ $tableNumber }}</h5>
                                            <span class="table-status {{ $allDelivered ? 'delivered' : 'pending' }}">
                                                {{ $allDelivered ? 'Ödeme Bekliyor' : 'Açık' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="table-body">
                                        <div class="orders-summary">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted">{{ $orders->count() }} sipariş</span>
                                                    <i class="bi bi-arrow-right-circle ms-2 text-primary" style="font-size: 1rem;"></i>
                                                </div>
                                                <span class="fw-bold">Son: {{ $orders->sortByDesc('created_at')->first()->created_at->format('H:i') }}</span>
                                            </div>
                                            
                                            <div class="total-amount">
                                                <h4 class="text-primary mb-0">₺{{ number_format($totalAmount, 2) }}</h4>
                                                <small class="text-muted">Toplam Tutar</small>
                                        </div>
                                    </div>
                                    
                                        @if($allDelivered)
                                            <div class="text-center mt-3">
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="event.stopPropagation(); showPaymentModal('{{ $tableNumber }}', {{ $totalAmount }})">
                                                    <i class="bi bi-credit-card"></i> Ödeme Al
                                        </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-check-circle fs-1"></i>
                                    <h5 class="mt-3">Tüm masalar kapalı!</h5>
                                    <p>Ödeme bekleyen masa yok.</p>
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
                                            @if($order->customer_name)
                                                <br><small class="text-primary">{{ $order->customer_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('H:i') }}</td>
                                        <td>{{ $order->updated_at->format('H:i') }}</td>
                                        <td>
                                            @if($order->payment_method)
                                                @php
                                                    $paymentData = json_decode($order->payment_method, true);
                                                @endphp
                                                @if(is_array($paymentData) && isset($paymentData['methods']))
                                                    @foreach($paymentData['methods'] as $method)
                                                        <span class="badge {{ $method['method'] === 'nakit' ? 'bg-success' : 'bg-primary' }} me-1">
                                                            {{ ucfirst($method['method']) }} ₺{{ number_format($method['amount'], 2) }}
                                            </span>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-primary">{{ $order->payment_method }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Bilinmiyor</span>
                                            @endif
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

<!-- Masa Detay Modal -->
<div class="modal fade" id="tableDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-table me-2"></i>
                    Masa <span id="detailTableNumber"></span> Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tableDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ödeme Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card me-2"></i>
                    Ödeme Al - Masa <span id="paymentTableNumber"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4 p-4 bg-light rounded">
                    <h2 class="text-primary mb-0">₺<span id="paymentTotalAmount"></span></h2>
                    <small class="text-muted">Toplam Ödeme Tutarı</small>
                </div>
                
                <form id="paymentForm">
                    <div class="row" id="paymentMethods">
                        <!-- Ödeme yöntemleri dinamik olarak eklenecek -->
                    </div>
                    
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Toplam Ödeme:</span>
                            <span class="fw-bold" id="totalPaymentDisplay">₺0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Kalan:</span>
                            <span class="fw-bold" id="remainingAmountDisplay">₺0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" id="changeRow" style="display: none;">
                            <span class="text-success">Para Üstü:</span>
                            <span class="fw-bold text-success" id="changeAmountDisplay">₺0.00</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetPaymentModal()">İptal</button>
                <button type="button" class="btn btn-outline-primary me-2" onclick="addPaymentMethod()">
                    <i class="bi bi-plus-circle"></i> Ödeme Yöntemi Ekle
                </button>
                <button type="button" class="btn btn-success" onclick="processTablePayment()" id="completePaymentBtn" disabled>
                    <i class="bi bi-check-circle"></i>
                    Ödemeyi Tamamla
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Gün Sonu PDF Modal -->
<div class="modal fade" id="endOfDayPdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-pdf me-2"></i>
                    Gün Sonu Raporu (PDF)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="min-height: 80vh;">
                <iframe id="endOfDayPdfFrame" src="" style="width:100%;height:70vh;border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <a id="downloadEndOfDayPdf" href="#" class="btn btn-primary" download>
                    <i class="bi bi-download"></i> PDF İndir
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
.table-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    height: 100%;
}

.table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.table-card:hover .bi-arrow-right-circle {
    transform: translateX(3px);
    transition: transform 0.2s ease;
}

.table-card.has-pending {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
}

.table-card.ready-for-payment {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
}

.table-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.table-status.pending {
    background: #ffc107;
    color: #000;
}

.table-status.delivered {
    background: #28a745;
    color: white;
}

.total-amount {
    text-align: center;
    padding: 1rem 0;
}

.payment-method-row {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8f9fa;
}

.payment-method-row:last-child {
    margin-bottom: 0;
}

.remove-payment {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    padding: 0;
    line-height: 1;
}
</style>
@endsection

@section('scripts')
<script>
    let currentTableNumber = null;
    let currentTotalAmount = 0;
    let paymentMethods = [];

    // Masa detaylarını göster
    function showTableDetails(tableNumber) {
        currentTableNumber = tableNumber;
        
        document.getElementById('detailTableNumber').textContent = tableNumber;
        
        const modal = new bootstrap.Modal(document.getElementById('tableDetailsModal'));
        modal.show();
        
        // AJAX ile masa detaylarını yükle
        fetch(`{{ url('restaurant/tables') }}/${encodeURIComponent(tableNumber)}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTableDetails(data);
            } else {
                document.getElementById('tableDetailsContent').innerHTML = 
                    '<div class="alert alert-danger">' + (data.message || 'Masa detayları yüklenemedi') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tableDetailsContent').innerHTML = 
                '<div class="alert alert-danger">Masa detayları yüklenirken hata oluştu</div>';
        });
    }

    // Masa detaylarını görüntüle
    function displayTableDetails(data) {
        let html = `
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Toplam Sipariş:</h6>
                        <p class="fw-bold">${data.order_count} adet</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Toplam Tutar:</h6>
                        <p class="fw-bold text-primary fs-4">₺${data.total_amount.toFixed(2)}</p>
                    </div>
                </div>
            </div>
            
            <div class="orders-list">
                <h6>Siparişler:</h6>
        `;
        
        data.orders.forEach((order, orderIndex) => {
            const statusBadge = getStatusBadge(order.status);
            const orderTime = new Date(order.created_at).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
            const orderTotal = parseFloat(order.total) || 0;
            
            html += `
                <div class="order-detail mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Sipariş #${order.id}</h6>
                        <div>
                            ${statusBadge}
                            <small class="text-muted ms-2">${orderTime}</small>
                        </div>
                    </div>
                    
                    ${order.customer_name ? `<p class="text-primary mb-2"><i class="bi bi-person"></i> ${order.customer_name}</p>` : ''}
                    ${order.created_by ? `<p class="text-muted mb-2"><i class="bi bi-person-badge"></i> Garson: ${order.created_by.name}</p>` : ''}
                    
                    <div class="order-items">
            `;
            
            if (order.order_items && Array.isArray(order.order_items)) {
                order.order_items.forEach(item => {
                    const itemPrice = parseFloat(item.price) || 0;
                    const itemQuantity = parseInt(item.quantity) || 0;
                    const itemTotal = itemPrice * itemQuantity;
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span class="fw-medium">${item.product ? item.product.name : 'Bilinmeyen Ürün'}</span>
                                ${item.note ? `<br><small class="text-muted fst-italic">${item.note}</small>` : ''}
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">${itemQuantity}x</span>
                                <br><span class="fw-bold">₺${itemTotal.toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                });
            }
            
            html += `
                    </div>
                    <div class="text-end mt-2">
                        <span class="fw-bold">Sipariş Toplamı: ₺${orderTotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
        
        // Tümü delivered ise ödeme butonu ekle
        const allDelivered = data.orders.every(order => order.status === 'delivered');
        if (allDelivered) {
            html += `
                <div class="text-center mt-4">
                    <button class="btn btn-success btn-lg" onclick="showPaymentModal('${data.table_number}', ${data.total_amount})">
                        <i class="bi bi-credit-card"></i> Ödeme Al (₺${data.total_amount.toFixed(2)})
                    </button>
                </div>
            `;
        }
        
        document.getElementById('tableDetailsContent').innerHTML = html;
    }

    // Status badge helper
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Bekliyor</span>',
            'preparing': '<span class="badge bg-info">Hazırlanıyor</span>',
            'ready': '<span class="badge bg-primary">Hazır</span>',
            'delivered': '<span class="badge bg-success">Teslim Edildi</span>',
            'completed': '<span class="badge bg-dark">Tamamlandı</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    // Ödeme modalını göster
    function showPaymentModal(tableNumber, totalAmount) {
        currentTableNumber = tableNumber;
        currentTotalAmount = totalAmount;
        
        // Ödeme yöntemlerini sıfırla
        resetPaymentModal();
        
        document.getElementById('paymentTableNumber').textContent = tableNumber;
        document.getElementById('paymentTotalAmount').textContent = totalAmount.toFixed(2);
        
        // İlk ödeme yöntemini ekle
        addPaymentMethod();
        
        // Modal'ı kapat (eğer masa detay modalı açıksa)
        const tableDetailsModal = bootstrap.Modal.getInstance(document.getElementById('tableDetailsModal'));
        if (tableDetailsModal) {
            tableDetailsModal.hide();
            // Kısa bir gecikme ile yeni modalı aç
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            }, 300);
        } else {
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }
    }

    // Ödeme modalını sıfırla
    function resetPaymentModal() {
        paymentMethods = [];
        document.getElementById('paymentMethods').innerHTML = '';
        document.getElementById('totalPaymentDisplay').textContent = '₺0.00';
        document.getElementById('remainingAmountDisplay').textContent = '₺0.00';
        document.getElementById('changeRow').style.display = 'none';
        
        const completeBtn = document.getElementById('completePaymentBtn');
        completeBtn.disabled = true;
        completeBtn.className = 'btn btn-success';
        completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Ödemeyi Tamamla';
    }

    // Ödeme yöntemi ekle
    function addPaymentMethod() {
        const methodId = 'payment_' + Date.now();
        const remaining = currentTotalAmount - paymentMethods.reduce((sum, p) => sum + p.amount, 0);
        
        const html = `
            <div class="col-12 position-relative payment-method-row" data-method-id="${methodId}">
                <button type="button" class="btn btn-sm btn-danger remove-payment" onclick="removePaymentMethod('${methodId}')">
                    <i class="bi bi-x"></i>
                </button>
                
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Ödeme Yöntemi</label>
                        <select class="form-select payment-method-select" data-method-id="${methodId}" onchange="updatePaymentMethod('${methodId}')">
                            <option value="nakit">Nakit</option>
                            <option value="kart">Kart</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Tutar</label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" class="form-control payment-amount" 
                                   data-method-id="${methodId}" 
                                   step="0.01" min="0" max="${remaining.toFixed(2)}" 
                                   value="${remaining.toFixed(2)}"
                                   onchange="updatePaymentMethod('${methodId}')">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('paymentMethods').insertAdjacentHTML('beforeend', html);
        
        // Ödeme yöntemini listeye ekle
        paymentMethods.push({
            id: methodId,
            method: 'nakit',
            amount: remaining
        });
        
        updatePaymentSummary();
    }

    // Ödeme yöntemini kaldır
    function removePaymentMethod(methodId) {
        if (paymentMethods.length <= 1) {
            alert('En az bir ödeme yöntemi gereklidir!');
            return;
        }
        
        // DOM'dan kaldır
        document.querySelector(`[data-method-id="${methodId}"]`).remove();
        
        // Listeden kaldır
        paymentMethods = paymentMethods.filter(p => p.id !== methodId);
        
        updatePaymentSummary();
    }

    // Ödeme yöntemini güncelle
    function updatePaymentMethod(methodId) {
        const methodElement = document.querySelector(`select[data-method-id="${methodId}"]`);
        const amountElement = document.querySelector(`input[data-method-id="${methodId}"]`);
        
        const method = methodElement.value;
        const amount = parseFloat(amountElement.value) || 0;
        
        // Listede güncelle
        const paymentIndex = paymentMethods.findIndex(p => p.id === methodId);
        if (paymentIndex !== -1) {
            paymentMethods[paymentIndex] = {
                id: methodId,
                method: method,
                amount: amount
            };
        }
        
        updatePaymentSummary();
    }

    // Ödeme özetini güncelle
    function updatePaymentSummary() {
        const totalPaid = paymentMethods.reduce((sum, p) => sum + p.amount, 0);
        const remaining = currentTotalAmount - totalPaid;
        const change = remaining < 0 ? Math.abs(remaining) : 0;
        
        document.getElementById('totalPaymentDisplay').textContent = `₺${totalPaid.toFixed(2)}`;
        document.getElementById('remainingAmountDisplay').textContent = `₺${Math.max(0, remaining).toFixed(2)}`;
        
        const changeRow = document.getElementById('changeRow');
        const changeDisplay = document.getElementById('changeAmountDisplay');
        
        if (change > 0) {
            changeRow.style.display = 'flex';
            changeDisplay.textContent = `₺${change.toFixed(2)}`;
        } else {
            changeRow.style.display = 'none';
        }
        
        // Ödeme tamamlama butonunu kontrol et
        const completeBtn = document.getElementById('completePaymentBtn');
        if (Math.abs(remaining) < 0.01) { // Küsurat toleransı
            completeBtn.disabled = false;
            completeBtn.className = 'btn btn-success';
        } else if (remaining < 0) {
            completeBtn.disabled = false;
            completeBtn.className = 'btn btn-warning';
            completeBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Para Üstü ile Tamamla';
        } else {
            completeBtn.disabled = true;
            completeBtn.className = 'btn btn-success';
            completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Ödemeyi Tamamla';
        }
    }

    // Masa ödemeini işle
    function processTablePayment() {
        if (paymentMethods.length === 0) {
            alert('Ödeme yöntemi eklemelisiniz!');
            return;
        }
        
        const paymentData = {
            table_number: currentTableNumber,
            payments: paymentMethods.map(p => ({
                method: p.method,
                amount: p.amount
            })),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        fetch('{{ route("restaurant.cashier.process-table-payment") }}', {
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
                showSuccessMessage(data.message);
                
                // Para üstü varsa göster
                const totalPaid = paymentMethods.reduce((sum, p) => sum + p.amount, 0);
                const change = totalPaid - currentTotalAmount;
                if (change > 0.01) {
                        showSuccessMessage(`Para üstü: ₺${change.toFixed(2)}`);
                }
                
                // Modal'ı kapat ve sayfayı yenile
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                setTimeout(() => location.reload(), 1000);
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

    // Modal event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModal = document.getElementById('paymentModal');
        if (paymentModal) {
            paymentModal.addEventListener('hidden.bs.modal', function() {
                resetPaymentModal();
            });
        }
    });

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

    document.getElementById('endOfDayReportBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('endOfDayPdfModal'));
        const pdfUrl = '{{ route('restaurant.cashier.endofday.pdf') }}';
        document.getElementById('endOfDayPdfFrame').src = pdfUrl;
        document.getElementById('downloadEndOfDayPdf').href = pdfUrl;
        modal.show();
    });
</script>
@endsection 