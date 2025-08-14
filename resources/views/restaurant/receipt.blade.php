<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiş - {{ $restaurant->name }}</title>
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
            <div><strong>FİŞ NO:</strong> #{{ $order->id }}</div>
            <div><strong>MASA:</strong> {{ $order->table_number }}</div>
            @if($order->customer_name)
                <div><strong>MÜŞTERİ:</strong> {{ $order->customer_name }}</div>
            @endif
            <div><strong>TARİH:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</div>
            <div><strong>KASIYER:</strong> {{ auth()->user()->name }}</div>
            
            @if(in_array($order->status, ['kitchen_cancelled', 'cancelled', 'musteri_iptal', 'zafiyat']))
                <div style="margin-top: 10px; padding: 5px; border: 1px solid #ccc; background-color: #f8f9fa;">
                    @if($order->status === 'kitchen_cancelled')
                        <div style="color: #dc3545; font-weight: bold;">⚠️ MUTFAK İPTALİ</div>
                        <div style="font-size: 10px; color: #666;">Bu ürünün fiyatı alınmamaktadır.</div>
                    @elseif($order->status === 'cancelled')
                        <div style="color: #dc3545; font-weight: bold;">❌ İPTAL EDİLDİ</div>
                        <div style="font-size: 10px; color: #666;">Bu ürünün fiyatı alınmamaktadır.</div>
                    @elseif($order->status === 'musteri_iptal')
                        <div style="color: #dc3545; font-weight: bold;">❌ MÜŞTERİ İPTALİ</div>
                        <div style="font-size: 10px; color: #666;">Bu ürünün fiyatı alınmamaktadır.</div>
                    @elseif($order->status === 'zafiyat')
                        <div style="color: #ffc107; font-weight: bold;">⚠️ ZAFİYAT</div>
                        <div style="font-size: 10px; color: #666;">Bu ürünün fiyatı alınacaktır.</div>
                    @endif
                </div>
            @endif
        </div>

        <div class="items">
            <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
                <span>ÜRÜN</span>
                <span style="width: 30px; text-align: center;">ADET</span>
                <span style="width: 60px; text-align: right;">TUTAR</span>
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
        </div>

        @php
            $effectiveTotal = $order->total;
            $cancelledAmount = 0;
            
            if(in_array($order->status, ['kitchen_cancelled', 'cancelled', 'musteri_iptal'])) {
                $effectiveTotal = 0;
                $cancelledAmount = $order->total;
            }
        @endphp
        
        @php
            $isCancelled = $order->isCancelled();
            $isZafiyat = $order->isZafiyat();
        @endphp
        <div class="total-section">
            <div class="total-line">
                <span>ARA TOPLAM:</span>
                <span style="{{ $isCancelled ? 'text-decoration: line-through; color: #dc3545;' : '' }}">
                    ₺{{ number_format($order->total, 2) }}
                </span>
                @if($isCancelled)
                    <div style="font-size: 10px; color: #dc3545; margin-top: 2px;">(İptal - Fiyat alınmıyor)</div>
                @elseif($isZafiyat)
                    <div style="font-size: 10px; color: #ffc107; margin-top: 2px;">(Zafiyat - Fiyat alınacak)</div>
                @endif
            </div>
            
            @if($cancelledAmount > 0)
                <div class="total-line" style="color: #dc3545;">
                    <span>İPTAL EDİLEN:</span>
                    <span>-₺{{ number_format($cancelledAmount, 2) }}</span>
                </div>
            @endif
            
            @if($order->payments && $order->payments->count() > 0)
                @foreach($order->payments as $payment)
                    <div class="total-line">
                        <span>ÖDEME ({{ strtoupper($payment->payment_method) }}):</span>
                        <span>₺{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @if($payment->note)
                        <div class="total-line" style="font-size: 10px; color: #666;">
                            <span>Not: {{ $payment->note }}</span>
                        </div>
                    @endif
                @endforeach
                
                @php
                    $totalPaid = $order->payments->sum('amount');
                @endphp
                
                @if($totalPaid > $effectiveTotal)
                    <div class="total-line">
                        <span>PARA ÜSTÜ:</span>
                        <span>₺{{ number_format($totalPaid - $effectiveTotal, 2) }}</span>
                    </div>
                @endif
            @endif
            
            <div class="total-line total-amount" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                <span>TOPLAM:</span>
                <span>₺{{ number_format($effectiveTotal, 2) }}</span>
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