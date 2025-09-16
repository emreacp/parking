<?php
// .env dosyasından konfigürasyonu yükle
require_once 'config.php';

// Veritabanı bağlantısını .env dosyasından al
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASSWORD;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
