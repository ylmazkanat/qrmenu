@extends('layouts.business')

@section('title', 'Restoran Değerlendirmeleri - QR Menu')
@section('page-title', $restaurant->name . ' - Değerlendirmeler')

@section('content')
    <!-- Başlık -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">{{ $restaurant->name }} - Değerlendirmeler</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-4 text-center">
                                    <div class="h5 text-primary mb-0">{{ $restaurant->total_reviews }}</div>
                                    <small class="text-muted">Değerlendirme</small>
                                </div>
                                <div class="me-4 text-center">
                                    <div class="h5 text-warning mb-0">{{ number_format($restaurant->average_rating, 1) }}</div>
                                    <small class="text-muted">5 üzerinden ortalama</small>
                                </div>
                                <a href="{{ route('business.restaurants.show', $restaurant->id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Geri Dön
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Değerlendirme Listesi -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-star me-2"></i>
                        Değerlendirme Listesi
                    </h5>
                    <div class="d-flex align-items-center">
                        <select class="form-control me-2" id="sortSelect" style="width: 150px;">
                            <option value="desc">Yeniden Eskiye</option>
                            <option value="asc">Eskiden Yeniye</option>
                        </select>
                        <input type="text" class="form-control" id="searchInput" placeholder="İsim, e-posta veya yorum ara..." style="width: 300px;">
                    </div>
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Müşteri</th>
                                        <th>Puan</th>
                                        <th>Yorum</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                        <tr data-review-id="{{ $review->id }}" 
                                            data-rating="{{ $review->rating }}" 
                                            data-customer="{{ $review->customer_name }}"
                                            data-email="{{ $review->customer_email }}"
                                            data-comment="{{ $review->comment }}">
                                            <td>
                                                <div>
                                                    <strong>{{ $review->customer_name ?: 'Anonim' }}</strong>
                                                    @if($review->customer_email)
                                                        <br><small class="text-muted">{{ $review->customer_email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">{{ $review->rating }}/5</small>
                                            </td>
                                            <td>
                                                @if($review->comment)
                                                    <div class="text-truncate" style="max-width: 400px;" title="{{ $review->comment }}">
                                                        {{ $review->comment }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Yorum yok</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $review->created_at->format('d.m.Y') }}</div>
                                                <small class="text-muted">{{ $review->created_at->format('H:i') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-star text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Henüz değerlendirme yok</h5>
                            <p class="text-muted">Müşteriler değerlendirme yaptığında burada görünecek.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
// Arama ve sıralama sistemi
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript yüklendi');
    
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    
    console.log('Search input bulundu:', searchInput);
    console.log('Sort select bulundu:', sortSelect);
    
    // Arama fonksiyonu
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Arama yapılıyor:', this.value);
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const customerName = row.querySelector('td:first-child strong').textContent.toLowerCase();
                const customerEmail = row.querySelector('td:first-child small') ? row.querySelector('td:first-child small').textContent.toLowerCase() : '';
                const comment = row.querySelector('td:nth-child(3) div') ? row.querySelector('td:nth-child(3) div').textContent.toLowerCase() : '';
                
                if (searchTerm === '' || 
                    customerName.includes(searchTerm) || 
                    customerEmail.includes(searchTerm) || 
                    comment.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Sıralama fonksiyonu
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            console.log('Sıralama değişti:', this.value);
            const sortOrder = this.value;
            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Sadece görünür satırları sırala
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            
            visibleRows.sort((a, b) => {
                const dateA = a.querySelector('td:last-child div').textContent;
                const dateB = b.querySelector('td:last-child div').textContent;
                
                // Tarih formatını dönüştür (dd.mm.yyyy -> yyyy-mm-dd)
                const partsA = dateA.split('.');
                const partsB = dateB.split('.');
                const formattedDateA = partsA[2] + '-' + partsA[1] + '-' + partsA[0];
                const formattedDateB = partsB[2] + '-' + partsB[1] + '-' + partsB[0];
                
                if (sortOrder === 'desc') {
                    return new Date(formattedDateB) - new Date(formattedDateA); // Yeniden eskiye
                } else {
                    return new Date(formattedDateA) - new Date(formattedDateB); // Eskiden yeniye
                }
            });
            
            // Sıralanmış satırları tabloya geri ekle
            visibleRows.forEach(row => {
                tbody.appendChild(row);
            });
        });
    }
});
</script> 