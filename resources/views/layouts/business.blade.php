@extends('layouts.master')

@section('sidebar-gradient', 'linear-gradient(135deg, #28a745 0%, #20c997 100%)')
@section('panel-color', '#28a745')
@section('panel-icon', 'bi bi-building')
@section('role-name', 'İşletme Sahibi')

@section('sidebar-menu')
    <div class="nav-item">
        <a href="{{ route('business.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('business.restaurants') }}" class="nav-link">
            <i class="bi bi-shop"></i>
            Restoranlarım
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('business.restaurants.create') }}" class="nav-link">
            <i class="bi bi-plus-circle"></i>
            Yeni Restoran
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('business.staff') }}" class="nav-link">
            <i class="bi bi-people"></i>
            Çalışan Yönetimi
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('business.analytics') }}" class="nav-link">
            <i class="bi bi-graph-up"></i>
            İstatistikler
        </a>
    </div>
@endsection

@section('additional-css')
<style>
    .sidebar {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }
    
    .user-avatar {
        background: #28a745 !important;
    }
</style>
@endsection 