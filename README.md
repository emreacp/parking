# ACP Parking - Akıllı Otopark Yönetim Sistemi

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Google Maps](https://img.shields.io/badge/Google%20Maps-API-red.svg)](https://developers.google.com/maps)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Proje Hakkında

**ACP Parking**, şehirlerdeki otoparkları akıllı bir şekilde yönetmek ve kullanıcılara kolay park deneyimi sunmak için geliştirilmiş modern bir web uygulamasıdır. Bu proje, **Tez Ödevi** kapsamında geliştirilmiştir.

### 🎯 Ana Özellikler

- 🗺️ **Google Maps Entegrasyonu** - Gerçek zamanlı harita görünümü ve konum takibi
- 📍 **Akıllı Otopark Arama** - Yakındaki otoparkları konum bazlı bulma
- 🚗 **Gerçek Zamanlı Durum** - Dolu/boş yer sayısı anlık takibi
- 👤 **Kullanıcı Yönetim Sistemi** - Kayıt, giriş ve profil yönetimi
- 🛣️ **Navigasyon Desteği** - Google Maps ile yol tarifi
- 📱 **Responsive Tasarım** - Mobil ve masaüstü uyumlu arayüz
- 🔐 **Güvenli API Yönetimi** - Environment variables ile güvenlik
- 👨‍💼 **Yönetici Paneli** - Otopark yönetimi ve istatistikler
- 📊 **Detaylı Raporlama** - Kullanıcı ve otopark istatistikleri

## 🛠️ Teknolojiler

- **Backend:** PHP 7.4+
- **Veritabanı:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Maps API:** Google Maps JavaScript API
- **Server:** Apache (XAMPP)
- **Güvenlik:** Environment Variables (.env)
- **Tasarım:** Modern CSS Grid & Flexbox

## 📦 Kurulum

### Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 8.0 veya üzeri
- Apache Web Server
- Google Maps API Anahtarı
- XAMPP (önerilen)

### Adım 1: Projeyi İndirin

```bash
git clone https://github.com/KULLANICI_ADINIZ/acp-parking.git
cd acp-parking
```

### Adım 2: Veritabanını Kurun

1. XAMPP'ı başlatın ve MySQL servisini çalıştırın
2. phpMyAdmin'e gidin (`http://localhost/phpmyadmin`)
3. Yeni veritabanı oluşturun: `otopark_db`
4. Gerekli tabloları oluşturun (users, otoparklar)

### Adım 3: Konfigürasyon

1. `.env.example` dosyasını `.env` olarak kopyalayın:
```bash
cp .env.example .env
```

2. `.env` dosyasını düzenleyin:
```env
# Veritabanı Ayarları
DB_HOST=localhost
DB_NAME=otopark_db
DB_USER=root
DB_PASSWORD=

# Google Maps API
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

### Adım 4: Google Maps API Anahtarı

1. [Google Cloud Console](https://console.cloud.google.com/)'a gidin
2. Yeni proje oluşturun veya mevcut projeyi seçin
3. "APIs & Services" > "Credentials" bölümüne gidin
4. "Create Credentials" > "API Key" seçin
5. API anahtarını `.env` dosyasına ekleyin

### Adım 5: Projeyi Çalıştırın

1. XAMPP'da Apache ve MySQL servislerini başlatın
2. Projeyi `http://localhost/Tez` adresinden açın
3. Ana sayfadan kayıt olun veya giriş yapın

## 📁 Proje Yapısı

```
acp-parking/
├── 📄 index.php              # Ana sayfa (video arka planlı)
├── 📄 harita.php             # Google Maps entegrasyonu
├── 📄 giris_kayit.html       # Kullanıcı giriş/kayıt sayfası
├── 📄 yonetici_panel.php     # Yönetici kontrol paneli
├── 📄 otopark_ekle.php       # Otopark ekleme formu
├── 📄 otopark_guncelle.php   # Otopark güncelleme
├── 📄 otopark_sil.php        # Otopark silme
├── 📄 otopark_onayla.php     # Otopark onaylama
├── 📄 kullanici_islemleri.php # Kullanıcı işlemleri
├── 📄 iletisim.php           # İletişim sayfası
├── 📄 db.php                 # Veritabanı bağlantısı
├── 📄 config.php             # Konfigürasyon yönetimi
├── 📄 navbar.php             # Navigasyon menüsü
├── 📄 style.css              # Ana CSS dosyası
├── 📁 img/                   # Proje görselleri
│   ├── tanitim.mp4          # Ana sayfa video
│   ├── logo.jpg             # Logo
│   └── otopark.png          # Otopark ikonları
├── 📁 belgeler/             # Proje belgeleri
├── 📄 .env                  # Hassas bilgiler (git'e yüklenmez)
├── 📄 .env.example          # Örnek konfigürasyon
├── 📄 .gitignore            # Git ignore kuralları
└── 📄 README.md             # Bu dosya
```

## 🚀 Kullanım

### Kullanıcı Olarak

1. **Kayıt Olun** - Ana sayfadan "Kayıt Ol" butonuna tıklayın
2. **Giriş Yapın** - E-posta ve şifrenizle giriş yapın
3. **Otopark Arayın** - Harita sayfasında konumunuzu belirleyin
4. **Yol Tarifi Alın** - İstediğiniz otoparka navigasyon alın
5. **Durum Kontrolü** - Otoparkların dolu/boş durumunu görün

### Yönetici Olarak

1. **Yönetici Paneli** - `/yonetici_panel.php` adresine gidin
2. **Otopark Ekleme** - Yeni otopark bilgilerini girin
3. **Durum Güncelleme** - Dolu/boş yer sayılarını güncelleyin
4. **İstatistikler** - Kullanıcı ve otopark verilerini görün
5. **Onay İşlemleri** - Bekleyen otoparkları onaylayın

## 🔒 Güvenlik Özellikleri

- ✅ **API Anahtarları Gizli** - `.env` dosyasında saklanır
- ✅ **Veritabanı Güvenliği** - PDO prepared statements
- ✅ **XSS Koruması** - `htmlspecialchars()` kullanımı
- ✅ **Session Yönetimi** - Güvenli kullanıcı oturumu
- ✅ **Şifre Şifreleme** - `password_hash()` ile güvenli şifreleme
- ✅ **SQL Injection Koruması** - Parametreli sorgular

## 📊 Veritabanı Şeması

### Users Tablosu
```sql
- id (INT, PRIMARY KEY)
- ad (VARCHAR) - Kullanıcı adı
- soyad (VARCHAR) - Kullanıcı soyadı
- email (VARCHAR, UNIQUE) - E-posta adresi
- sifre (VARCHAR) - Şifrelenmiş şifre
- kayit_tarihi (DATETIME) - Kayıt tarihi
```

### Otoparklar Tablosu
```sql
- id (INT, PRIMARY KEY)
- ad (VARCHAR) - Otopark adı
- il (VARCHAR) - İl bilgisi
- latitude (DECIMAL) - Enlem koordinatı
- longitude (DECIMAL) - Boylam koordinatı
- ucret (DECIMAL) - Saatlik ücret
- dolu_yer_sayisi (INT) - Dolu yer sayısı
- bos_yer_sayisi (INT) - Boş yer sayısı
- onayli (BOOLEAN) - Onay durumu
- tarih (DATETIME) - Eklenme tarihi
```

## ✨ Özellikler

### Ana Sayfa
- Video arka planlı modern tasarım
- Son eklenen otoparklar kartları
- Responsive grid layout
- Smooth animasyonlar

### Harita Sayfası
- Google Maps entegrasyonu
- Gerçek zamanlı konum takibi
- Otopark işaretçileri
- Yol tarifi özelliği
- Konum bazlı arama

### Yönetici Paneli
- İstatistik dashboard'u
- Otopark yönetimi
- Kullanıcı yönetimi
- Onay işlemleri
- Raporlama

## 🤝 Katkıda Bulunma

1. Projeyi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🙏 Teşekkürler

- Google Maps API ekibi
- PHP Community
- MySQL Documentation
- XAMPP Development Team
- Modern web standartları

## 📸 Ekran Görüntüleri

> Proje görselleri `belgeler/` klasöründe bulunmaktadır.

---

⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
