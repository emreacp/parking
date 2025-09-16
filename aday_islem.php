<?php
require 'db.php';
session_start();

$emre_email = "emreacp@gmail.com";
if (!isset($_SESSION['kullanici_email']) || $_SESSION['kullanici_email'] !== $emre_email) {
  header("Location: index.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['islem'])) {
    $islem = $_POST['islem'];

    switch ($islem) {
        case 'onayla':
            $email = $_POST['email'];
            $stmt = $pdo->prepare("UPDATE users SET rol = 'admin', onay_tarihi = CURDATE() WHERE email = ?");

            $stmt->execute([$email]);
            break;
        case 'reddet':
            $email = $_POST['email'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute([$email]);
            break;
        case 'onayla_otopark':
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE otoparklar SET onayli = 1 WHERE id = ?");
            $stmt->execute([$id]);
            break;
        case 'reddet_otopark':
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM otoparklar WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
}

// Ek olarak GET ile gelen istekleri de destekle (örnek: ?reddet=email@adres.com)
if (isset($_GET['reddet'])) {
    $email = $_GET['reddet'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$email]);
    header("Location: yonetici_panel.php");
    exit;
}

header("Location: yonetici_panel.php");
exit;
?>