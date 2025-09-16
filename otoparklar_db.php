<?php
require 'db.php';
header('Content-Type: application/json');

$data = $pdo->query("SELECT ad, il, tür, latitude, longitude, ucret, kapanis_saati, dolu_yer_sayisi, bos_yer_sayisi FROM otoparklar WHERE onayli = 1")
            ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
