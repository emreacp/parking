<?php
require 'db.php';
session_start();

$emre_email = "emreacp@gmail.com";
if (!isset($_SESSION['kullanici_email']) || $_SESSION['kullanici_email'] !== $emre_email) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM otoparklar WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: yonetici_panel.php");
exit;
?>
