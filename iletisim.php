<?php
session_start();
require 'db.php';

$form_sonuc = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["konu"])) {
    $konu = $_POST["konu"];
    $diger = $_POST["diger_metin"] ?? "";
    $email = "emreacp@gmail.com";
    $mesaj = $konu === "diger" ? $diger : $konu;
    $headers = "From: site@acpparking.com\r\n";

    if (mail($email, "Kullanıcı İletişimi", $mesaj, $headers)) {
        $form_sonuc = "✅ Mesajınız iletildi. Teşekkür ederiz!";
    } else {
        $form_sonuc = "❌ Mesaj gönderilemedi. Lütfen daha sonra tekrar deneyin.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["yorum_ad"])) {
    $ad = htmlspecialchars(trim($_POST["yorum_ad"]));
    $yorum = htmlspecialchars(trim($_POST["yorum_metin"]));
    if ($ad && $yorum) {
        $stmt = $pdo->prepare("INSERT INTO yorumlar (ad, yorum) VALUES (?, ?)");
        $stmt->execute([$ad, $yorum]);
    }
}

$yorumlar = $pdo->query("SELECT * FROM yorumlar ORDER BY tarih DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>ACP Parking | İletişim</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #f1f9ff, #e0f7fa);
    }
    .container {
      display: flex;
      flex-wrap: wrap;
      padding: 40px;
      gap: 30px;
      justify-content: center;
    }
    .card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      padding: 30px;
      width: 420px;
    }
    .card h2 {
      margin-top: 0;
      color: #00acc1;
    }
    label {
      margin-top: 15px;
      display: block;
      font-weight: bold;
    }
    select, textarea, input[type="text"] {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
    }
    button {
      background: #00acc1;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      margin-top: 20px;
      cursor: pointer;
    }
    button:hover {
      background: #00838f;
    }
    .message-box {
      background: #e0f2f1;
      color: #00695c;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .social-icons {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    .social-icons img {
      width: 28px;
      height: 28px;
      transition: transform 0.3s;
    }
    .social-icons img:hover {
      transform: scale(1.2);
    }
    .yorumlar {
      margin-top: 30px;
    }
    .yorum-card {
      background: #f1f8e9;
      border-left: 5px solid #8bc34a;
      padding: 12px;
      border-radius: 8px;
      margin-top: 10px;
    }
    .yorum-card strong {
      color: #558b2f;
    }
    .yorum-card small {
      float: right;
      color: #777;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="card">
    <h2>Vizyon & Misyon</h2>
    <p><strong>Vizyon:</strong> Şehir içi park deneyimini dijitalleştirerek herkes için kolay ulaşılabilir hale getirmek.</p>
    <p><strong>Misyon:</strong> Kullanıcı dostu akıllı otopark sistemi ile şehir trafiğini azaltmak.</p>
    <h2>İletişim</h2>
    <p>📍 Antalya<br>📞 +90 555 123 4567<br>✉️ info@acpparking.com</p>
    <div class="social-icons">
      <a href="https://instagram.com/acpparking" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"></a>
      <a href="mailto:info@acpparking.com"><img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Mail"></a>
      <a href="https://wa.me/905551234567" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp"></a>
    </div>
  </div>

  <div class="card">
    <h2>İletişim Formu</h2>
    <?php if ($form_sonuc): ?>
      <div class="message-box"><?= $form_sonuc ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Konu</label>
      <select name="konu" onchange="toggleTextArea(this.value)">
        <option value="">-- Seçiniz --</option>
        <option value="Giriş yapamıyorum">Giriş yapamıyorum</option>
        <option value="Harita açılmıyor">Harita açılmıyor</option>
        <option value="Otopark bilgileri yanlış">Otopark bilgileri yanlış</option>
        <option value="diger">Diğer</option>
      </select>
      <div id="diger_kutu">
        <label for="diger_metin"></label>
        <textarea name="diger_metin" id="diger_metin"></textarea>
      </div>
      <button type="submit">Gönder</button>
    </form>
  </div>

  <div class="card">
    <h2>Kullanıcı Yorumları</h2>
    <form method="post">
      <input type="text" name="yorum_ad" placeholder="Adınız" required>
      <textarea name="yorum_metin" placeholder="Yorumunuz" required></textarea>
      <button type="submit">Yorum Yap</button>
    </form>

    <div class="yorumlar">
      <?php foreach ($yorumlar as $y): ?>
        <div class="yorum-card">
          <strong><?= htmlspecialchars($y["ad"]) ?></strong>
          <small><?= $y["tarih"] ?></small><br>
          <?= nl2br(htmlspecialchars($y["yorum"])) ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function toggleTextArea(value) {
  const kutu = document.getElementById("diger_metin");
  if (value === "diger") {
    kutu.style.display = "block";
    kutu.required = true;
  } else {
    kutu.style.display = "none";
    kutu.required = false;
  }
}
</script>
</body>
</html>
