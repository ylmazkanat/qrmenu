@extends('layouts.master')

@section('title', 'Erişim Reddedildi - QR Menu')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <h1 class="display-1 text-danger">403</h1>
                <h2 class="mb-4">Erişim Reddedildi</h2>
                <p class="text-muted mb-4">
                    Bu sayfaya erişim yetkiniz bulunmamaktadır.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="bi bi-house"></i>
                        Ana Sayfa
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 4rem 0;
}
.error-page h1 {
    font-size: 6rem;
    font-weight: 300;
}
</style>
@endsection 