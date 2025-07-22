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
        </div>

        <div class="items">
            <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
                <span>ÜRÜN</span>
                <span style="width: 30px; text-align: center;">ADET</span>
                <span style="width: 60px; text-align: right;">TUTAR</span>
            </div>
            
            @foreach($order->orderItems as $item)
                <div class="item">
                    <div class="item-name">
                        {{ $item->product->name }}
                        @if($item->note)
                            <div style="font-size: 10px; color: #666;">Not: {{ $item->note }}</div>
                        @endif
                    </div>
                    <div class="item-qty">{{ $item->quantity }}</div>
                    <div class="item-price">₺{{ number_format($item->price * $item->quantity, 2) }}</div>
                </div>
                <div style="font-size: 10px; color: #666; margin-left: 0; margin-bottom: 5px;">
                    {{ $item->quantity }} x ₺{{ number_format($item->price, 2) }}
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-line">
                <span>ARA TOPLAM:</span>
                <span>₺{{ number_format($order->total, 2) }}</span>
            </div>
            
            @if($order->payment_method)
                <div class="total-line">
                    <span>ÖDEME YÖNTEMİ:</span>
                    <span>{{ strtoupper($order->payment_method) }}</span>
                </div>
                
                @if($order->payment_method === 'nakit' && $order->cash_received)
                    <div class="total-line">
                        <span>ALINAN:</span>
                        <span>₺{{ number_format($order->cash_received, 2) }}</span>
                    </div>
                    @if($order->cash_received > $order->total)
                        <div class="total-line">
                            <span>PARA ÜSTÜ:</span>
                            <span>₺{{ number_format($order->cash_received - $order->total, 2) }}</span>
                        </div>
                    @endif
                @endif
            @endif
            
            <div class="total-line total-amount" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                <span>TOPLAM:</span>
                <span>₺{{ number_format($order->total, 2) }}</span>
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