@extends('layouts.master')

@section('sidebar-gradient', 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)')
@section('panel-color', '#dc3545')
@section('panel-icon', 'bi bi-shield-check')
@section('role-name', 'Sistem Yöneticisi')

@section('sidebar-menu')
    <div class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.businesses') }}" class="nav-link">
            <i class="bi bi-building"></i>
            İşletme Yönetimi
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.restaurants') }}" class="nav-link">
            <i class="bi bi-shop"></i>
            Restoran Yönetimi
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.users') }}" class="nav-link">
            <i class="bi bi-people"></i>
            Kullanıcı Yönetimi
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('admin.analytics') }}" class="nav-link">
            <i class="bi bi-graph-up"></i>
            Sistem İstatistikleri
        </a>
    </div>
@endsection

@section('additional-css')
<style>
    .sidebar {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%) !important;
    }
    
    .user-avatar {
        background: #dc3545 !important;
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        color: white;
    }
</style>
@endsection 