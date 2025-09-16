<?php
session_start();
require 'db.php';
$sonOtoparklar = $pdo->query("SELECT * FROM otoparklar WHERE onayli = 1 ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="ACP Parking: Akıllı otopark sistemleriyle kolay park deneyimi." />
  <meta name="robots" content="index, follow" />
  <meta name="author" content="ACP Parking Ekibi" />
  <meta property="og:title" content="ACP Parking" />
  <meta property="og:description" content="Akıllı otopark çözümleriyle tanışın." />
  <meta property="og:image" content="https://example.com/assets/img/logo.png" /> <!-- Yayına geçince düzenle -->
  <meta property="og:url" content="https://acpparking.com/" /> <!-- Canlı URL ile değiştir -->
  <meta property="og:type" content="website" />
  <meta name="twitter:card" content="summary_large_image" />
  <title>Ana Sayfa - ACP Parking</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
      color: white;
    }

    .video-container {
      position: relative;
      width: 100%;
      height:80vh;
      overflow: hidden;
    }

    .video-container video {
      position: absolute;
      top: 20%;
      left: 50%;
      min-width: 100%;
      min-height: 100%;
      transform: translate(-50%, -50%);
      object-fit: cover;
      z-index: -1;
    }

    .video-overlay {
      position: absolute;
      top: 35%;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
      z-index: 1;
    }

    .video-overlay h1 {
      font-size: 56px;
      margin-bottom: 20px;
      text-shadow: 0 3px 6px rgba(0,0,0,0.7);
    }

    .video-overlay p {
      font-size: 22px;
      margin-bottom: 30px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.6);
    }

    .video-overlay button {
      padding: 14px 28px;
      font-size: 18px;
      background: #00c853;
      border: none;
      border-radius: 10px;
      color: white;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: 0.3s;
    }

    .video-overlay button:hover {
      background: #00b248;
      transform: scale(1.05);
    }

   .cards-title {
  text-align: center;
  font-size: 32px;
  margin-top: 0px;
  margin-bottom: 0px;
  color: #fdd835;
  text-shadow: 0 2px 6px rgba(0,0,0,0.5);
  
}

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      padding: 40px;
      background: #111;

    }

    .otopark-card {
      background: rgba(255,255,255,0.08);
      border-radius: 12px;
      padding: 20px;
      color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
      transition: transform 0.3s;
    }

    .otopark-card:hover {
      transform: translateY(-5px);
      background: rgba(255,255,255,0.12);
    }

    .otopark-card h4 {
      margin-top: 0;
      color: #00e676;
    }

    .otopark-card .btn {
      margin-top: 10px;
      background: #2962ff;
      color: white;
      border: none;
      padding: 10px 14px;
      border-radius: 6px;
      cursor: pointer;
    }

    .otopark-card .btn:hover {
      background: #0039cb;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="video-container">
  <video autoplay muted loop playsinline>
    <source src="img/tanitim.mp4" type="video/mp4">
    Tarayıcınız video etiketini desteklemiyor.
  </video>
  <div class="video-overlay">
    <h1>ACP PARKING</h1>
    <p>Şehrinizdeki otoparkları kolayca bulun!</p>
    <?php if (!isset($_SESSION['kullanici_email'])): ?>
      <button onclick="alert('Lütfen giriş yapınız.')">Hemen Başla</button>
    <?php else: ?>
      <button onclick="window.location.href='harita.php#konum'">Hemen Başla</button>
    <?php endif; ?>
  </div>
</div>

<h2 class="cards-title">Son Eklenen Otoparklar</h2>
<div class="card-grid">
  <?php foreach ($sonOtoparklar as $o): ?>
    <div class="otopark-card">
      <h4><?= htmlspecialchars($o['ad']) ?></h4>
      <p><strong>İl:</strong> <?= htmlspecialchars($o['il']) ?></p>
      <p><strong>Ücret:</strong> <?= $o['ucret'] ?> ₺</p>
      <p><strong>Dolu:</strong> <?= $o['dolu_yer_sayisi'] ?> - <strong>Boş:</strong> <?= $o['bos_yer_sayisi'] ?></p>
      <button class="btn" onclick="window.location.href='harita.php?lat=<?= $o['latitude'] ?>&lon=<?= $o['longitude'] ?>&ad=<?= urlencode($o['ad']) ?>'">Haritada Gör</button>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
