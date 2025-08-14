<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masa Fişi - {{ $restaurant->name }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            max-width: 300px;
        }
        .receipt {
            text-align: center;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .restaurant-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .order-info {
            text-align: left;
            margin: 15px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .items {
            text-align: left;
            margin: 15px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .item-name {
            flex: 1;
        }
        .item-qty {
            width: 30px;
            text-align: center;
        }
        .item-price {
            width: 60px;
            text-align: right;
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 5px;
            margin: 10px 0 5px 0;
            border-left: 3px solid #007bff;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            margin-left: 5px;
        }
        .status-cancelled { background-color: #dc3545; color: white; }
        .status-zafiyat { background-color: #ffc107; color: black; }
        .status-delivered { background-color: #28a745; color: white; }
        .status-pending { background-color: #6c757d; color: white; }
        .total-section {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 15px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-amount {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 10px;
        }
        .payment-section {
            margin: 15px 0;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="restaurant-name">{{ $restaurant->name }}</div>
            @if($restaurant->phone)
                <div>Tel: {{ $restaurant->phone }}</div>
            @endif
            @if($restaurant->address)
                <div style="font-size: 10px;">{{ $restaurant->address }}</div>
            @endif
        </div>

        <div class="order-info">
            <div><strong>MASA FİŞİ</strong></div>
            <div><strong>MASA:</strong> {{ $tableNumber }}</div>
            <div><strong>TARİH:</strong> {{ now()->format('d.m.Y H:i') }}</div>
            <div><strong>KASIYER:</strong> {{ auth()->user()->name }}</div>
            <div><strong>TOPLAM SİPARİŞ:</strong> {{ $tableOrders->count() }} adet</div>
        </div>

        <div class="items">
            <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
                <span>ÜRÜN</span>
                <span style="width: 30px; text-align: center;">ADET</span>
                <span style="width: 60px; text-align: right;">TUTAR</span>
            </div>
            
            @foreach($tableOrders as $order)
                <div class="order-header">
                    Sipariş #{{ $order->id }}
                    @php
                        $displayStatus = $order->getDisplayStatus();
                    @endphp
                    @if($displayStatus === 'kitchen_cancelled')
                        <span class="status-badge status-cancelled">MUTFAK İPTALİ</span>
                        <span style="font-size: 10px; color: #dc3545;">(Müşteri İptal)</span>
                    @elseif($displayStatus === 'cancelled')
                        <span class="status-badge status-cancelled">İPTAL EDİLDİ</span>
                        <span style="font-size: 10px; color: #dc3545;">(Müşteri İptal)</span>
                    @elseif($displayStatus === 'musteri_iptal')
                        <span class="status-badge status-cancelled">MÜŞTERİ İPTALİ</span>
                        <span style="font-size: 10px; color: #dc3545;">(Müşteri İptal)</span>
                    @elseif($displayStatus === 'zafiyat')
                        <span class="status-badge status-zafiyat">ZAFİYAT</span>
                        <span style="font-size: 10px; color: #ffc107;">(Zafiyat)</span>
                    @elseif($displayStatus === 'delivered')
                        <span class="status-badge status-delivered">TESLİM EDİLDİ</span>
                    @else
                        <span class="status-badge status-pending">{{ strtoupper($displayStatus) }}</span>
                    @endif
                    <div style="font-size: 10px; color: #666;">{{ $order->created_at->format('H:i') }}</div>
                </div>
                
                @foreach($order->orderItems as $item)
                    @php
                        $isCancelled = $item->is_cancelled ?? false;
                        $isZafiyat = $item->is_zafiyat ?? false;
                    @endphp
                    <div class="item">
                        <div class="item-name">
                            @if($item->product)
                                {{ $item->product->name }}
                            @else
                                Silinmiş Ürün
                            @endif
                            @if($item->note)
                                <div style="font-size: 10px; color: #666;">Not: {{ $item->note }}</div>
                            @endif
                        </div>
                        <div class="item-qty">{{ $item->quantity }}</div>
                        <div class="item-price" style="{{ $isCancelled ? 'text-decoration: line-through; color: #dc3545;' : '' }}">
                            ₺{{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                    <div style="font-size: 10px; color: {{ $isCancelled ? '#dc3545' : '#666' }}; margin-left: 0; margin-bottom: 5px;">
                        {{ $item->quantity }} x ₺{{ number_format($item->price, 2) }}
                        @if($isCancelled)
                            <span style="color: #dc3545;">(İptal)</span>
                        @elseif($isZafiyat)
                            <span style="color: #ffc107;">(Zafiyat)</span>
                        @endif
                    </div>
                @endforeach
                
                @php
                    $isCancelled = $order->isCancelled();
                    $isZafiyat = $order->isZafiyat();
                @endphp
                <div style="text-align: right; margin-bottom: 10px; font-weight: bold;">
                    Sipariş Toplamı: 
                    <span style="{{ $isCancelled ? 'text-decoration: line-through; color: #dc3545;' : '' }}">
                        ₺{{ number_format($order->total, 2) }}
                    </span>
                    @if($isCancelled)
                        <div style="font-size: 10px; color: #dc3545;">❌ Fiyat alınmıyor (İptal)</div>
                    @elseif($isZafiyat)
                        <div style="font-size: 10px; color: #ffc107;">⚠️ Fiyat alınacak (Zafiyat)</div>
                    @endif
                </div>
            @endforeach
        </div>

        @php
            $allPayments = collect();
            foreach($tableOrders as $order) {
                foreach($order->payments as $payment) {
                    $allPayments->push($payment);
                }
            }
            $paymentsByMethod = $allPayments->groupBy('payment_method');
        @endphp

        @if($allPayments->count() > 0)
            <div class="payment-section">
                <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;">
                    YAPILAN ÖDEMELER
                </div>
                @foreach($paymentsByMethod as $method => $payments)
                    <div class="total-line">
                        <span>{{ strtoupper($method) }}:</span>
                        <span>₺{{ number_format($payments->sum('amount'), 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="total-section">
            <div class="total-line">
                <span>TOPLAM SİPARİŞ TUTARI:</span>
                <span>₺{{ number_format($tableOrders->sum('total'), 2) }}</span>
            </div>
            
            @if($cancelledAmount > 0)
                <div class="total-line" style="color: #dc3545;">
                    <span>İPTAL EDİLEN:</span>
                    <span>-₺{{ number_format($cancelledAmount, 2) }}</span>
                </div>
            @endif
            
            <div class="total-line">
                <span>ÖDENECEK TUTAR:</span>
                <span>₺{{ number_format($totalAmount, 2) }}</span>
            </div>
            
            @if($totalPaid > 0)
                <div class="total-line">
                    <span>TOPLAM ÖDENEN:</span>
                    <span>₺{{ number_format($totalPaid, 2) }}</span>
                </div>
                
                @if($totalPaid > $totalAmount)
                    <div class="total-line">
                        <span>PARA ÜSTÜ:</span>
                        <span>₺{{ number_format($totalPaid - $totalAmount, 2) }}</span>
                    </div>
                @endif
            @endif
            
            <div class="total-line total-amount" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                <span>KALAN:</span>
                <span>₺{{ number_format($totalAmount - $totalPaid, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <div>QR Menu Sistemi</div>
            <div>{{ now()->format('d.m.Y H:i:s') }}</div>
            <div style="margin-top: 10px;">
                Bizi tercih ettiğiniz için teşekkürler!
            </div>
            @if($restaurant->website)
                <div>{{ $restaurant->website }}</div>
            @endif
        </div>
    </div>

    <script>
        // Sayfa yüklendiğinde otomatik yazdır
        window.onload = function() {
            window.print();
            
            // Yazdırma tamamlandıktan sonra pencereyi kapat
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html> 