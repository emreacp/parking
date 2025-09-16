<?php
require 'db.php';
session_start();

if (!isset($_SESSION['kullanici_email']) || ($_SESSION['kullanici_email'] !== 'emreacp@gmail.com' && $_SESSION['rol'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $ad = $_POST['ad'];
    $il = $_POST['il'];
    $ucret = $_POST['ucret'];
    $dolu = $_POST['dolu'];
    $bos = $_POST['bos'];
    

    // Güncelleme öncesi veriyi al
    $eski = $pdo->prepare("SELECT * FROM otoparklar WHERE id = ?");
    $eski->execute([$id]);
    $eskiVeri = $eski->fetch(PDO::FETCH_ASSOC);

    // Güncelleme sorgusu
    $stmt = $pdo->prepare("UPDATE otoparklar SET ad = ?, il = ?, ucret = ?, dolu_yer_sayisi = ?, bos_yer_sayisi = ? WHERE id = ?");
    $success = $stmt->execute([$ad, $il, $ucret, $dolu, $bos, $id]);

    if ($success) {
        // Değişiklik türünü belirle
        $degisiklik = "";
        if ($eskiVeri['ucret'] != $ucret) {
            $degisiklik = "Ücret bilgisi güncellendi ({$eskiVeri['ucret']} ₺ → {$ucret} ₺)";
        } elseif ($eskiVeri['ad'] != $ad) {
            $degisiklik = "Otopark adı değiştirildi ({$eskiVeri['ad']} → {$ad})";
        } elseif ($eskiVeri['il'] != $il) {
            $degisiklik = "İl bilgisi değiştirildi ({$eskiVeri['il']} → {$il})";
        } elseif ($eskiVeri['dolu_yer_sayisi'] != $dolu) {
            $degisiklik = "Dolu yer bilgisi güncellendi ({$eskiVeri['dolu_yer_sayisi']} → {$dolu})";
        } elseif ($eskiVeri['bos_yer_sayisi'] != $bos) {
            $degisiklik = "Boş yer bilgisi güncellendi ({$eskiVeri['bos_yer_sayisi']} → {$bos})";
    
        } else {
            $degisiklik = "Otopark bilgileri güncellendi";
        }

        // Bildirimi kaydet
        $bildirimEkle = $pdo->prepare("INSERT INTO bildirimler (otopark_id, ad, degisiklik, guncelleyen) VALUES (?, ?, ?, ?)");
        $bildirimEkle->execute([$id, $ad, $degisiklik, $_SESSION['kullanici_email']]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
    }
    exit;
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}
?>
