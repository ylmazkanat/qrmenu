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
                                // İptal edilen siparişleri hariç tut
                                $activeOrders = $orders->whereNotIn('status', ['kitchen_cancelled', 'cancelled', 'musteri_iptal']);
                                $totalAmount = $activeOrders->sum('total');
                                $totalPaid = $activeOrders->sum('paid_amount');
                                $remainingAmount = $totalAmount - $totalPaid;
                                $hasDelivered = $orders->where('status', 'delivered')->count() > 0;
                                $allDelivered = $orders->every(fn($order) => $order->status === 'delivered');
                                $hasUnpaidOrders = $activeOrders->where('payment_status', '!=', 'paid')->count() > 0;
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="table-card {{ $hasUnpaidOrders ? 'ready-for-payment' : 'has-pending' }}" 
                                     data-table="{{ $tableNumber }}" 
                                     onclick="showTableDetails('{{ $tableNumber }}')">
                                    <div class="table-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Masa {{ $tableNumber }}</h5>
                                            <span class="table-status {{ $hasUnpaidOrders ? 'delivered' : 'pending' }}">
                                                {{ $hasUnpaidOrders ? 'Ödeme Bekliyor' : 'Açık' }}
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
                                                @if($totalPaid > 0)
                                                    <div class="mt-2">
                                                        <small class="text-success">Ödenen: ₺{{ number_format($totalPaid, 2) }}</small>
                                                        <br>
                                                        <small class="text-danger">Kalan: ₺{{ number_format($remainingAmount, 2) }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($hasUnpaidOrders)
                                            <div class="text-center mt-3">
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="event.stopPropagation(); showPaymentModal('{{ $tableNumber }}', {{ $remainingAmount }}, null)">
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

    <!-- Yapılan Ödemeler -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Yapılan Ödemeler (Bugün)
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
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $order)
                                    <!-- Ana Satır -->
                                    <tr class="table-row-main" onclick="toggleOrderDetails(this)" style="cursor: pointer;">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-chevron-down me-2 text-muted toggle-icon"></i>
                                                <div>
                                                    <span class="fw-bold">Masa {{ $order->table_number }}</span>
                                                    @if($order->customer_name)
                                                        <br><small class="text-primary">{{ $order->customer_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $order->created_at->format('H:i') }}</td>
                                        <td>{{ $order->updated_at->format('H:i') }}</td>
                                        <td>
                                            @if($order->payments && $order->payments->count() > 0)
                                                @php
                                                    $paymentsByMethod = $order->payments->groupBy('payment_method');
                                                @endphp
                                                @foreach($paymentsByMethod as $method => $payments)
                                                    <span class="badge {{ $method === 'nakit' ? 'bg-success' : 'bg-primary' }} me-1">
                                                        {{ ucfirst($method) }} ₺{{ number_format($payments->sum('amount'), 2) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">Bilinmiyor</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">₺{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'partially_paid' ? 'warning' : 'danger') }}">
                                                {{ $order->payment_status == 'paid' ? 'Tam Ödendi' : ($order->payment_status == 'partially_paid' ? 'Kısmi Ödendi' : 'Ödenmedi') }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); printTableReceipt('{{ $order->table_number }}', '{{ $order->session_id }}')">
                                                <i class="bi bi-printer"></i>
                                                Masa Fişi
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Sipariş Detayları Satırı -->
                                    <tr class="table-row-details" style="display: none;">
                                        <td colspan="7" class="p-0">
                                            <div class="order-details-collapse" style="background-color: #f8f9fa; padding: 10px; border-top: 1px solid #dee2e6;">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <small class="text-muted fw-medium mb-2 d-block">
                                                            <i class="bi bi-list-ul me-1"></i>
                                                            Sipariş İçeriği:
                                                        </small>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($order->orderItems as $item)
                                                                @php
                                                                    $isCancelled = $item->is_cancelled ?? false;
                                                                    $isZafiyat = $item->is_zafiyat ?? false;
                                                                @endphp
                                                                <span class="badge bg-light text-dark border">
                                                                    @if($item->product)
                                                                        {{ $item->quantity }}x {{ Str::limit($item->product->name, 20) }}
                                                                    @else
                                                                        {{ $item->quantity }}x Silinmiş Ürün
                                                                    @endif
                                                                    @if($item->note)
                                                                        <small class="text-muted ms-1">({{ Str::limit($item->note, 15) }})</small>
                                                                    @endif
                                                                    @if($isCancelled)
                                                                        <small class="text-danger ms-1">(İptal)</small>
                                                                    @elseif($isZafiyat)
                                                                        <small class="text-warning ms-1">(Zafiyat)</small>
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                        @if($order->orderItems->count() > 8)
                                                            <small class="text-muted mt-2 d-block">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                Toplam {{ $order->orderItems->count() }} ürün sipariş edildi
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
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
                    <small class="text-muted">Kalan Ödeme Tutarı</small>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> 
                            Kısmi ödeme yapabilirsiniz. Müşteri istediği kadar ödeme yapabilir.
                        </small>
                    </div>
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
    position: relative;
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

.payment-history {
    border: 1px solid #e9ecef;
    background: #f8f9fa;
}

.payment-history-list {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white;
}

.payment-item {
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem;
}

.payment-item:last-child {
    border-bottom: none;
}

.payment-item:hover {
    background: #f8f9fa;
}
</style>
@endsection

@section('scripts')
<style>
    /* Tablo satır stilleri */
    .table-row-main {
        background-color: #ffffff;
        border-bottom: 1px solid #dee2e6;
    }
    
    .table-row-details {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table-row-details:hover {
        background-color: #e9ecef;
    }
    
    .order-details-collapse {
        transition: all 0.3s ease;
    }
    
    .order-details-collapse .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Ana satır hover efekti */
    .table-row-main:hover {
        background-color: #f8f9fa;
    }
    
    /* Responsive tasarım */
    @media (max-width: 768px) {
        .order-details-collapse .d-flex {
            flex-direction: column;
        }
        
        .order-details-collapse .badge {
            margin-bottom: 0.25rem;
        }
    }
</style>
<script>
    let currentTableNumber = null;
    let currentTotalAmount = 0;
    let currentOrderId = null;
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
            const orderPaid = parseFloat(order.paid_amount) || 0;
            const orderRemaining = orderTotal - orderPaid;
            
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
                        ${orderPaid > 0 ? `<br><small class="text-success">Ödenen: ₺${orderPaid.toFixed(2)}</small>` : ''}
                        ${['kitchen_cancelled', 'cancelled', 'musteri_iptal'].includes(order.status) ? 
                            `<br><small class="text-muted"><i class="bi bi-x-circle"></i> İptal edildi - Fiyat alınmıyor</small>` : 
                            (order.status === 'zafiyat' ? 
                                `<br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Zafiyat - Fiyat alınacak</small>` :
                                (orderRemaining > 0 ? `<br><small class="text-danger">Kalan: ₺${orderRemaining.toFixed(2)}</small>` : '')
                            )
                        }
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
        
        // Masa özeti ve kalan tutar (iptal edilenler hariç)
        const activeOrders = data.orders.filter(order => 
            !['kitchen_cancelled', 'cancelled', 'musteri_iptal'].includes(order.status)
        );
        const activeTotal = activeOrders.reduce((sum, order) => sum + parseFloat(order.total), 0);
        const activePaid = activeOrders.reduce((sum, order) => sum + parseFloat(order.paid_amount), 0);
        const activeRemaining = activeTotal - activePaid;
        
        html += `
            <div class="mt-4 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Masa Özeti:</h6>
                        <div class="d-flex justify-content-between">
                            <span>Toplam Tutar:</span>
                            <span class="fw-bold">₺${activeTotal.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Ödenen:</span>
                            <span class="fw-bold text-success">₺${activePaid.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Kalan:</span>
                            <span class="fw-bold ${activeRemaining < 0 ? 'text-warning' : 'text-danger'}">₺${activeRemaining.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Yapılan ödemeler bölümü (sadece açık siparişlerin ödemeleri)
        const allPayments = data.orders.flatMap(order => 
            order.payments ? order.payments.map(payment => ({
                ...payment,
                orderId: order.id
            })) : []
        ).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        
        if (allPayments.length > 0) {
            html += `
                <div class="mt-4">
                    <h6 class="mb-3"><i class="bi bi-clock-history"></i> Yapılan Ödemeler:</h6>
                    <div class="payment-history-list">
            `;
            
            allPayments.forEach(payment => {
                html += `
                    <div class="payment-item d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <small class="text-muted">${new Date(payment.created_at).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'})}</small>
                            <br>
                            <small class="text-primary">Sipariş #${payment.orderId}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge ${payment.payment_method === 'nakit' ? 'bg-success' : 'bg-primary'} fs-6 me-2">
                                ${payment.payment_method === 'nakit' ? 'Nakit' : 'Kart'} ₺${parseFloat(payment.amount).toFixed(2)}
                            </span>
                            <button class="btn btn-sm btn-outline-danger" onclick="deletePayment(${payment.id}, '${data.table_number}')" title="Ödemeyi Sil">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        // Ödenmemiş siparişler varsa ödeme butonu ekle (iptal edilenler hariç)
        const hasUnpaidOrders = activeOrders.some(order => {
            const orderPaid = parseFloat(order.paid_amount) || 0;
            const orderTotal = parseFloat(order.total) || 0;
            return orderPaid < orderTotal;
        });
        
        if (hasUnpaidOrders) {
            const totalRemaining = activeOrders.reduce((sum, order) => {
                const orderPaid = parseFloat(order.paid_amount) || 0;
                const orderTotal = parseFloat(order.total) || 0;
                return sum + (orderTotal - orderPaid);
            }, 0);
            
            html += `
                <div class="text-center mt-4">
                    <button class="btn btn-success btn-lg me-2" onclick="showPaymentModal('${data.table_number}', ${totalRemaining})">
                        <i class="bi bi-credit-card"></i> Ödeme Al (₺${totalRemaining.toFixed(2)})
                    </button>
                    <button class="btn btn-warning btn-lg" onclick="closeTable('${data.table_number}')">
                        <i class="bi bi-door-closed"></i> Masayı Kapat
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
            'completed': '<span class="badge bg-dark">Tamamlandı</span>',
            'zafiyat': '<span class="badge bg-warning">Zafiyat</span>',
            'kitchen_cancelled': '<span class="badge bg-danger">Mutfak İptali</span>',
            'cancelled': '<span class="badge bg-secondary">İptal Edildi</span>',
            'musteri_iptal': '<span class="badge bg-secondary">Müşteri İptali</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    // Ödeme modalını göster
    function showPaymentModal(tableNumber, totalAmount, orderId) {
        currentTableNumber = tableNumber;
        currentTotalAmount = totalAmount;
        currentOrderId = orderId;
        
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
        if (totalPaid > 0) { // Herhangi bir ödeme yapıldıysa
            completeBtn.disabled = false;
            if (Math.abs(remaining) < 0.01) { // Tam ödeme
                completeBtn.className = 'btn btn-success';
                completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Ödemeyi Tamamla';
            } else if (remaining < 0) { // Para üstü
                completeBtn.className = 'btn btn-warning';
                completeBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Para Üstü ile Tamamla';
            } else { // Kısmi ödeme
                completeBtn.className = 'btn btn-info';
                completeBtn.innerHTML = '<i class="bi bi-credit-card"></i> Kısmi Ödeme Al';
            }
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
            order_id: currentOrderId,
            payments: paymentMethods.map(p => ({
                method: p.method,
                amount: p.amount,
                note: p.note
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
                
                // Masa kapatıldıysa ek mesaj göster ve masa fişi yazdır
                if (data.table_closed) {
                    setTimeout(() => {
                        showSuccessMessage(`Masa ${currentTableNumber} başarıyla kapatıldı!`);
                        
                        // Sadece masa fişi yazdır (ayrı fişler yazdırma)
                        if (data.table_receipt_url) {
                            setTimeout(() => {
                                const printWindow = window.open(data.table_receipt_url, '_blank');
                                printWindow.onload = function() {
                                    printWindow.print();
                                };
                            }, 500);
                        }
                    }, 1500);
                }
                
                // Modal'ı kapat ve sayfayı yenile
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                setTimeout(() => location.reload(), 2000);
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

    // Masa fişi yazdır
    function printTableReceipt(tableNumber, sessionId = null) {
        let url = `{{ route("restaurant.cashier.print-table-receipt", [":tableNumber", ":sessionId"]) }}`.replace(':tableNumber', tableNumber);
        if (sessionId) {
            url = url.replace(':sessionId', sessionId);
        } else {
            url = url.replace('/:sessionId', '');
        }
        
        const printWindow = window.open(url, '_blank');
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

    // Ödeme silme fonksiyonu
    function deletePayment(paymentId, tableNumber) {
        if (confirm('Bu ödemeyi silmek istediğinizden emin misiniz?')) {
            fetch(`{{ url('restaurant/cashier/payment') }}/${paymentId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Ödeme başarıyla silindi!');
                    // Masa detaylarını yenile
                    showTableDetails(tableNumber);
                } else {
                    showErrorMessage('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Ödeme silinirken hata oluştu!');
            });
        }
    }

    // Masa kapatma fonksiyonu
    function closeTable(tableNumber) {
        const confirmMessage = 'Masayı kapatmak istediğinizden emin misiniz?\n\nBu işlem tüm siparişleri teslim edilmiş olarak işaretleyecek ve masayı kapatacaktır.';
        
        if (confirm(confirmMessage)) {
            fetch(`{{ url('restaurant/cashier/table') }}/${encodeURIComponent(tableNumber)}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Masa başarıyla kapatıldı!');
                    
                    // Masa fişi yazdır
                    setTimeout(() => {
                        const printWindow = window.open(`{{ route('restaurant.cashier.print-table-receipt', [':tableNumber', '']) }}`.replace(':tableNumber', tableNumber).replace('/""', ''), '_blank');
                        printWindow.onload = function() {
                            printWindow.print();
                        };
                    }, 1000);
                    
                    // Modal'ı kapat ve sayfayı yenile
                    bootstrap.Modal.getInstance(document.getElementById('tableDetailsModal')).hide();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showErrorMessage('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Masa kapatılırken hata oluştu!');
            });
        }
    }

    document.getElementById('endOfDayReportBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('endOfDayPdfModal'));
        const pdfUrl = '{{ route('restaurant.cashier.endofday.pdf') }}';
        document.getElementById('endOfDayPdfFrame').src = pdfUrl;
        document.getElementById('downloadEndOfDayPdf').href = pdfUrl;
        modal.show();
    });

    // Sipariş detaylarını aç/kapat
    function toggleOrderDetails(row) {
        const detailsRow = row.nextElementSibling;
        const toggleIcon = row.querySelector('.toggle-icon');
        
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
            toggleIcon.classList.remove('bi-chevron-down');
            toggleIcon.classList.add('bi-chevron-up');
            row.style.backgroundColor = '#f8f9fa';
        } else {
            detailsRow.style.display = 'none';
            toggleIcon.classList.remove('bi-chevron-up');
            toggleIcon.classList.add('bi-chevron-down');
            row.style.backgroundColor = '#ffffff';
        }
    }
</script>
@endsection 