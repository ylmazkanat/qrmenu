@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gün Sonu Raporu - {{ $restaurant->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary-table, .orders-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-table th, .summary-table td, .orders-table th, .orders-table td { border: 1px solid #ccc; padding: 6px; }
        .orders-table th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $restaurant->name }}</h2>
        <h4>Gün Sonu Raporu</h4>
        <p>{{ $today }}</p>
    </div>
    <table class="summary-table">
        <tr>
            <th>Toplam Sipariş</th>
            <td class="text-center">{{ $totalCount }}</td>
            <th>Toplam Ciro</th>
            <td class="text-right">₺{{ number_format($totalRevenue, 2) }}</td>
        </tr>
        <tr>
            <th>Ödeme Yöntemleri</th>
            <td colspan="3">
                @foreach($payments as $method => $amount)
                    <span>{{ ucfirst($method) }}: ₺{{ number_format($amount, 2) }}</span>@if(!$loop->last), @endif
                @endforeach
            </td>
        </tr>
    </table>
    <h4>Sipariş Listesi</h4>
    <table class="orders-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Masa</th>
                <th>Saat</th>
                <th>Tutar</th>
                <th>Ödeme</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td class="text-center">{{ $order->id }}</td>
                    <td class="text-center">{{ $order->table_number }}</td>
                    <td class="text-center">{{ Carbon::parse($order->created_at)->format('H:i') }}</td>
                    <td class="text-right">₺{{ number_format($order->total, 2) }}</td>
                    <td>
                        @php $paymentData = json_decode($order->payment_method, true); @endphp
                        @if(isset($paymentData['methods']))
                            @foreach($paymentData['methods'] as $method)
                                <span>{{ ucfirst($method['method']) }}: ₺{{ number_format($method['amount'], 2) }}</span>@if(!$loop->last), @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center" style="margin-top:40px;">
        <small>Oluşturulma: {{ now()->format('Y-m-d H:i') }}</small>
    </div>
</body>
</html> 