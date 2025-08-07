@extends('layouts.business')
@section('content')
<div class="container py-5">
    <h1 class="mb-4">Ödeme Sayfası</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Abonelik Ödemesi</h5>
            <p><strong>Paket:</strong> {{ $subscription->package->name }}</p>
            <p><strong>Tutar:</strong> {{ number_format($amount, 2) }} ₺</p>
            <form action="{{ route('business.packages.payment', ['subscription' => $subscription->id, 'amount' => $amount]) }}" method="POST">
                @csrf
                <!-- Ödeme bilgileri buraya eklenebilir: kredi kartı vs. -->
                <div class="mb-3">
                    <label for="card_number" class="form-label">Kart Numarası</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX">
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="expiry_date" class="form-label">Son Kullanma Tarihi</label>
                        <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="AA/YY">
                    </div>
                    <div class="col">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="XXX">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Ödemeyi Tamamla</button>
            </form>
        </div>
    </div>
</div>
@endsection