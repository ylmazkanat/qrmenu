# 🍽️ QR Menu - Dijital Menü ve Sipariş Sistemi

QR Menu, restoranlar için modern ve kullanımı kolay bir dijital menü ve sipariş yönetim sistemidir. Laravel tabanlı olarak geliştirilmiş bu sistem, QR kod ile erişilebilen menüler, online sipariş alma ve restoran yönetimi sunar.

## ✨ Özellikler

### 👥 Kullanıcı Rolleri
- **Admin**: Tüm sistem yönetimi
- **Restoran Sahibi**: Restoran ve menü yönetimi  
- **Garson**: Sipariş alma ve masa yönetimi
- **Mutfak**: Sipariş durumu takibi

### 🏪 Restoran Yönetimi
- Restoran oluşturma ve düzenleme
- Kategori ve ürün yönetimi
- QR kod ile menü erişimi
- Custom domain desteği
- Logo ve görsel yükleme

### 📱 Müşteri Arayüzü
- QR kod ile menü erişimi
- Kategorili ürün listesi
- Sepet yönetimi
- Masa numarası ile sipariş
- Responsive tasarım

### 📊 Sipariş Sistemi
- Real-time sipariş takibi
- Sipariş durum güncellemeleri
- Mutfak ekranı
- Garson paneli
- Sipariş geçmişi

### 🎨 Tasarım
- **Tabler UI** ile modern arayüz
- Bootstrap 5 tabanlı
- Responsive tasarım
- Temiz ve sade görünüm

## 🚀 Kurulum

### Gereksinimler
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Adım 1: Projeyi Klonlayın
```bash
git clone https://github.com/username/qrmenu.git
cd qrmenu
```

### Adım 2: Bağımlılıkları Kurun
```bash
composer install
npm install && npm run build
```

### Adım 3: Veritabanını Oluşturun
```sql
# database.sql dosyasını MySQL'de çalıştırın
mysql -u root -p < database.sql
```

### Adım 4: Environment Ayarları
```bash
# .env dosyası oluşturun
cp .env.example .env

# Uygulama anahtarı oluşturun
php artisan key:generate

# .env dosyasındaki veritabanı ayarlarını düzenleyin
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qrmenu_db
DB_USERNAME=root
DB_PASSWORD=
```

### Adım 5: Storage Linkini Oluşturun
```bash
php artisan storage:link
```

### Adım 6: Sunucuyu Başlatın
```bash
php artisan serve
```

## 📋 Test Hesapları

### Admin Hesabı
- **E-mail**: admin@qrmenu.com
- **Şifre**: password

### Restoran Hesabı  
- **E-mail**: test@restaurant.com
- **Şifre**: password

## 🔗 Test Menüsü
Kurulum sonrası test menüsüne şu adresten erişebilirsiniz:
```
http://localhost:8000/menu/test-restaurant
```

## 📱 API Endpoints (Mobil App İçin)

### Authentication
```bash
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
```

### Restaurant & Menu
```bash
GET /api/restaurants/{slug}
GET /api/restaurants/{slug}/menu
```

### Orders
```bash
POST /api/orders
GET /api/orders/{order}
```

## 🏗️ Proje Yapısı

```
qrmenu/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── Restaurant/RestaurantController.php
│   │   ├── Menu/MenuController.php
│   │   └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Restaurant.php
│   │   ├── Product.php
│   │   └── ...
│   └── Http/Middleware/RoleMiddleware.php
├── database/
│   └── migrations/
├── routes/
│   └── web.php
└── database.sql
```

## 🎯 Kullanım

### Restoran Oluşturma
1. Kayıt olun veya giriş yapın
2. "Yeni Restoran Oluştur" butonuna tıklayın
3. Restoran bilgilerini doldurun
4. Menü kategorileri ve ürünleri ekleyin

### QR Menü Oluşturma
- Restoran oluşturulduktan sonra otomatik olarak şu formatta URL oluşur:
- `yoursite.com/menu/{restaurant-slug}`
- Bu URL'yi QR kod haline getirerek masalara yerleştirin

### Sipariş Alma
1. Müşteri QR kodu okutarak menüye erişir
2. Ürünleri sepete ekler
3. Masa numarasını girer
4. Siparişi tamamlar
5. Restoran panelinden sipariş görünür

## 🔧 Geliştirme

### Yeni Özellik Ekleme
1. Model oluşturun: `php artisan make:model ModelName`
2. Migration oluşturun: `php artisan make:migration create_table_name`
3. Controller oluşturun: `php artisan make:controller ControllerName`
4. Route ekleyin: `routes/web.php`

### Veritabanı Değişiklikleri
```bash
php artisan make:migration migration_name
php artisan migrate
```

## 📊 Planlanan Özellikler

- [ ] Garson & Mutfak ekranları
- [ ] QR kod ile sipariş alma
- [ ] Admin panel istatistikleri  
- [ ] Üyelik & Faturalandırma sistemi
- [ ] Flutter mobil uygulaması
- [ ] Çoklu dil desteği
- [ ] WhatsApp entegrasyonu
- [ ] Ödeme sistemi entegrasyonu

## 🛠️ Teknolojiler

- **Backend**: Laravel 11
- **Frontend**: Tabler UI + Bootstrap 5
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **File Storage**: Laravel Storage

## 📞 Destek

Sorularınız için:
- GitHub Issues kullanın
- E-mail: support@qrmenu.com

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

---

**QR Menu** - Modern restoranlar için dijital çözüm 🚀
