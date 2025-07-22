<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Menu - Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .test-account-btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            margin: 0.1rem;
        }
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card login-card">
                        <div class="card-header bg-white text-center border-0 pt-4">
                            <h3 class="mb-1">🍽️ QR Menu</h3>
                            <p class="text-muted">Hesabınıza giriş yapın</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" id="loginForm">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium">
                                        <i class="bi bi-envelope me-1"></i>
                                        E-mail
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" placeholder="email@example.com" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-medium">
                                        <i class="bi bi-lock me-1"></i>
                                        Şifre
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Şifrenizi girin" required>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Beni Hatırla
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-login text-white w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Giriş Yap
                                </button>
                            </form>
                            
                            <!-- Test Hesapları -->
                            <div class="border-top pt-3">
                                <h6 class="text-center mb-3">
                                    <i class="bi bi-people me-1"></i>
                                    Test Hesapları
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button class="btn btn-outline-danger test-account-btn w-100" 
                                                onclick="fillLogin('admin@qrmenu.com', 'password')">
                                            <i class="bi bi-shield-check"></i>
                                            Admin
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-success test-account-btn w-100" 
                                                onclick="fillLogin('isletme@qrmenu.com', 'password')">
                                            <i class="bi bi-building"></i>
                                            İşletme
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary test-account-btn w-100" 
                                                onclick="fillLogin('mudur@restaurant.com', 'password')">
                                            <i class="bi bi-person-badge"></i>
                                            Müdür
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-info test-account-btn w-100" 
                                                onclick="fillLogin('garson1@restaurant.com', 'password')">
                                            <i class="bi bi-person"></i>
                                            Garson
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-warning test-account-btn w-100" 
                                                onclick="fillLogin('asci1@restaurant.com', 'password')">
                                            <i class="bi bi-fire"></i>
                                            Mutfak
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-secondary test-account-btn w-100" 
                                                onclick="fillLogin('kasiyer1@restaurant.com', 'password')">
                                            <i class="bi bi-cash-coin"></i>
                                            Kasiyer
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted">Hızlı giriş için butona tıklayın</small>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="text-center">
                                <p class="mb-0">Hesabınız yok mu? 
                                    <a href="{{ route('register') }}" class="text-decoration-none">Kayıt Ol</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Test hesap bilgilerini doldur
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            
            // Form'u otomatik gönder
            setTimeout(() => {
                document.getElementById('loginForm').submit();
            }, 300);
        }

        // Enter tuşu ile form gönderme
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html> 