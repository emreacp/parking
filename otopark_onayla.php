<?php
require 'db.php';
session_start();

$emre_email = "emreacp@gmail.com"; // yöneticinin maili
if (!isset($_SESSION['kullanici_email']) || $_SESSION['kullanici_email'] !== $emre_email) {
    header("Location: index.php");
    exit;
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("UPDATE otoparklar SET onayli = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: yonetici_panel.php");
    exit;
}

if (isset($_GET['reddet'])) {
    $id = intval($_GET['reddet']);
    $stmt = $pdo->prepare("DELETE FROM otoparklar WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: yonetici_panel.php");
    exit;
}
?>
