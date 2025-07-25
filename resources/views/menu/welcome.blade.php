@extends('layouts.menu')

@section('content')
<div class="container mt-5">
    <div class="text-center">
        @if($restaurant->logo)
            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="img-fluid mb-4" style="max-width: 200px;">
        @endif
        
        <h1 class="mb-4">{{ $restaurant->name }}</h1>
        
        <div class="social-icons mb-4">
            @if($restaurant->instagram)
                <a href="https://instagram.com/{{ $restaurant->instagram }}" target="_blank" class="me-3">
                    <i class="fab fa-instagram fa-2x"></i>
                </a>
            @endif
            @if($restaurant->facebook)
                <a href="{{ $restaurant->facebook }}" target="_blank" class="me-3">
                    <i class="fab fa-facebook fa-2x"></i>
                </a>
            @endif
        </div>

        @if($restaurant->description)
            <p class="mb-5">{{ $restaurant->description }}</p>
        @endif

        <a href="{{ route('menu.categories', $restaurant->slug) }}" class="btn btn-lg btn-primary">
            Menüyü Görüntüle
        </a>
    </div>
</div>
@endsection
