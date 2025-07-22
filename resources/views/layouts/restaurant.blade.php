@extends('layouts.master')

@section('sidebar-gradient', 'linear-gradient(135deg, #007bff 0%, #6f42c1 100%)')
@section('panel-color', '#007bff')
@section('panel-icon', 'bi bi-shop')
@section('role-name', 'Restoran Operasyonları')

@section('sidebar-menu')
    <div class="nav-item">
        <a href="{{ route('restaurant.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
    </div>
    
    @if(auth()->user()->isRestaurantManager())
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                Restoran Yönetimi
            </a>
        </div>
    @endif
    
    @if(auth()->user()->isWaiter() || auth()->user()->isRestaurantManager())
        <div class="nav-item">
            <a href="{{ route('restaurant.waiter') }}" class="nav-link">
                <i class="bi bi-person-badge"></i>
                Garson Paneli
                <span class="notification-badge" id="waiter-badge" style="display: none;">0</span>
            </a>
        </div>
    @endif
    
    @if(auth()->user()->isKitchen() || auth()->user()->isRestaurantManager())
        <div class="nav-item">
            <a href="{{ route('restaurant.kitchen') }}" class="nav-link">
                <i class="bi bi-fire"></i>
                Mutfak Paneli
                <span class="notification-badge" id="kitchen-badge" style="display: none;">0</span>
            </a>
        </div>
    @endif
    
    @if(auth()->user()->isCashier() || auth()->user()->isRestaurantManager())
        <div class="nav-item">
            <a href="{{ route('restaurant.cashier') }}" class="nav-link">
                <i class="bi bi-cash-coin"></i>
                Kasiyer Paneli
                <span class="notification-badge" id="cashier-badge" style="display: none;">0</span>
            </a>
        </div>
    @endif
@endsection

@section('header-notifications')
    @if(auth()->user()->isWaiter() || auth()->user()->isKitchen() || auth()->user()->isCashier() || auth()->user()->isRestaurantManager())
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger" id="total-notifications" style="display: none;">0</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Bildirimler</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li id="no-notifications">
                    <span class="dropdown-item-text text-muted">Yeni bildirim yok</span>
                </li>
            </ul>
        </div>
    @endif
@endsection

@section('additional-css')
<style>
    .sidebar {
        background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%) !important;
    }
    
    .user-avatar {
        background: #007bff !important;
    }
</style>
@endsection

@section('scripts')
<script>
    // Real-time sipariş takibi
    function updateOrderNotifications() {
        fetch('{{ route("restaurant.api.orders.updates") }}')
            .then(response => response.json())
            .then(data => {
                // Garson bildirimleri
                const waiterBadge = document.getElementById('waiter-badge');
                if (data.ready_orders > 0) {
                    waiterBadge.textContent = data.ready_orders;
                    waiterBadge.style.display = 'inline';
                } else {
                    waiterBadge.style.display = 'none';
                }
                
                // Mutfak bildirimleri  
                const kitchenBadge = document.getElementById('kitchen-badge');
                if (data.pending_orders > 0) {
                    kitchenBadge.textContent = data.pending_orders;
                    kitchenBadge.style.display = 'inline';
                } else {
                    kitchenBadge.style.display = 'none';
                }
                
                // Kasiyer bildirimleri
                const cashierBadge = document.getElementById('cashier-badge');
                if (data.payment_pending > 0) {
                    cashierBadge.textContent = data.payment_pending;
                    cashierBadge.style.display = 'inline';
                } else {
                    cashierBadge.style.display = 'none';
                }
                
                // Toplam bildirim
                const totalNotifications = document.getElementById('total-notifications');
                const total = data.ready_orders + data.pending_orders + data.payment_pending;
                if (total > 0) {
                    totalNotifications.textContent = total;
                    totalNotifications.style.display = 'inline';
                } else {
                    totalNotifications.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Bildirim güncellenirken hata:', error);
            });
    }
    
    // Her 10 saniyede bir kontrol et
    @if(auth()->user()->isWaiter() || auth()->user()->isKitchen() || auth()->user()->isCashier() || auth()->user()->isRestaurantManager())
        setInterval(updateOrderNotifications, 10000);
        // Sayfa yüklendiğinde de çalıştır
        document.addEventListener('DOMContentLoaded', updateOrderNotifications);
    @endif
</script>
@endsection 