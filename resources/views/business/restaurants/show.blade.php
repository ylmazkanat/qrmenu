@extends('layouts.business')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $restaurant->name }}</h4>
                <div class="d-flex align-items-center">
                    <a href="{{ route('business.restaurants.reviews', $restaurant->id) }}" class="btn btn-primary me-2">
                        {{ __('Değerlendirmeler') }}
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRestaurantModal">
                        {{ __('Restoranı Sil') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Restaurant Modal -->
    <div class="modal fade" id="deleteRestaurantModal" tabindex="-1" aria-labelledby="deleteRestaurantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRestaurantModalLabel">{{ __('Restoranı Sil') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('business.restaurants.delete', $restaurant->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>{{ __('Bu restoranı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.') }}</p>
                        <div class="form-group">
                            <label for="confirmName">{{ __('Onaylamak için restoran adını yazın:') }} <strong>{{ $restaurant->name }}</strong></label>
                            <input type="text" class="form-control mt-2" id="confirmName" name="confirm_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('İptal') }}</button>
                        <button type="submit" class="btn btn-danger" id="deleteButton" disabled>{{ __('Sil') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rest of your existing content -->
</div>

@push('scripts')
<script>
document.getElementById('confirmName').addEventListener('input', function() {
    const deleteButton = document.getElementById('deleteButton');
    const restaurantName = '{{ $restaurant->name }}';
    
    if (this.value === restaurantName) {
        deleteButton.disabled = false;
    } else {
        deleteButton.disabled = true;
    }
});
</script>
@endpush

@endsection
