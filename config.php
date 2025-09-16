<?php
/**
 * .env dosyasını okumak için yardımcı fonksiyon
 * Bu dosya hassas bilgileri güvenli bir şekilde yönetir
 */

function loadEnv($path) {
    // .env dosyasının varlığını kontrol et
    if (!file_exists($path)) {
        throw new Exception('.env dosyası bulunamadı!');
    }
    
    // .env dosyasını satır satır oku
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    
    foreach ($lines as $line) {
        // Yorum satırlarını atla
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // KEY=VALUE formatını parse et
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Tırnak işaretlerini kaldır
            $value = trim($value, '"\'');
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

// .env dosyasını yükle
try {
    $env = loadEnv(__DIR__ . '/.env');
    
    // Veritabanı bilgilerini tanımla
    define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
    define('DB_NAME', $env['DB_NAME'] ?? 'acp_parking');
    define('DB_USER', $env['DB_USER'] ?? 'root');
    define('DB_PASSWORD', $env['DB_PASSWORD'] ?? '');
    
    // API anahtarlarını tanımla
    define('GOOGLE_MAPS_API_KEY', $env['GOOGLE_MAPS_API_KEY'] ?? '');
    
} catch (Exception $e) {
    // Hata durumunda varsayılan değerleri kullan
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'acp_parking');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('GOOGLE_MAPS_API_KEY', '');
    
    // Geliştirme ortamında hata göster
    if (isset($_GET['debug'])) {
        echo "Hata: " . $e->getMessage();
    }
}
?>
