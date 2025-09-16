<?php
session_start();
require 'db.php';

if (!isset($_SESSION['kullanici_email'])) {
    header("Location: giris.php");
    exit();
}

$email = $_SESSION['kullanici_email'];
$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = htmlspecialchars($_POST['ad']);
    $il = htmlspecialchars($_POST['il']);
    $ucret = (float) $_POST['ucret'];
    $dolu = (int) $_POST['dolu'];
    $bos = (int) $_POST['bos'];
    $kapanis_saati = htmlspecialchars($_POST['kapanis_saati']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);

   
$stmt = $pdo->prepare("INSERT INTO otoparklar (ad, il, ucret, dolu_yer_sayisi, bos_yer_sayisi, sahip_email, onayli, latitude, longitude, kapanis_saati)
                       VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, ?)");
    if ($stmt->execute([$ad, $il, $ucret, $dolu, $bos, $email, $latitude, $longitude, $kapanis_saati])) {
        $mesaj = "<div class='alert alert-success text-center fw-bold'>✅ Otopark başarıyla eklendi, yönetici onayı bekleniyor.</div>";
    } else {
        $mesaj = "<div class='alert alert-danger text-center fw-bold'>❌ Hata oluştu. Lütfen tekrar deneyin.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Yeni Otopark Ekle</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #c9d6ff, #e2e2e2);
      min-height: 100vh;
    }
    .form-container {
      max-width: 650px;
      margin: 60px auto;
      background: white;
      border-radius: 16px;
      padding: 30px 40px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .form-title {
      background: linear-gradient(to right,rgb(0, 255, 187), #0072ff);
      color: white;
      padding: 15px 20px;
      border-radius: 12px;
      text-align: center;
      margin-bottom: 30px;
      font-size: 22px;
      font-weight: bold;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .btn-custom {
      background-color: #00b894;
      color: white;
      font-weight: bold;
      border-radius: 10px;
    }
    .btn-custom:hover {
      background-color: #019875;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
  <div class="form-title">📍 Yeni Otopark Ekle</div>

  <?= $mesaj ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Otopark Adı</label>
      <input type="text" class="form-control" name="ad" placeholder="Örn: Beşiktaş Otoparkı" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Şehir</label>
      <select class="form-select" name="il" required>
        <option value="">Şehir Seçiniz</option>
        <?php
          $iller = ["Adana","Adıyaman","Afyonkarahisar","Ağrı","Amasya","Ankara","Antalya","Artvin","Aydın","Balıkesir",
                    "Bilecik","Bingöl","Bitlis","Bolu","Burdur","Bursa","Çanakkale","Çankırı","Çorum","Denizli",
                    "Diyarbakır","Edirne","Elazığ","Erzincan","Erzurum","Eskişehir","Gaziantep","Giresun","Gümüşhane",
                    "Hakkari","Hatay","Isparta","Mersin","İstanbul","İzmir","Kars","Kastamonu","Kayseri","Kırklareli",
                    "Kırşehir","Kocaeli","Konya","Kütahya","Malatya","Manisa","Kahramanmaraş","Mardin","Muğla",
                    "Muş","Nevşehir","Niğde","Ordu","Rize","Sakarya","Samsun","Siirt","Sinop","Sivas","Tekirdağ",
                    "Tokat","Trabzon","Tunceli","Şanlıurfa","Uşak","Van","Yozgat","Zonguldak","Aksaray","Bayburt",
                    "Karaman","Kırıkkale","Batman","Şırnak","Bartın","Ardahan","Iğdır","Yalova","Karabük","Kilis",
                    "Osmaniye","Düzce"];
          foreach ($iller as $sehir) {
              echo "<option value=\"$sehir\">$sehir</option>";
          }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Ücret (₺)</label>
      <input type="number" class="form-control" name="ucret" step="0.01" placeholder="Örn: 25.00" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Dolu Yer Sayısı</label>
      <input type="number" class="form-control" name="dolu" placeholder="Örn: 10" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Boş Yer Sayısı</label>
      <input type="number" class="form-control" name="bos" placeholder="Örn: 15" required>
    </div>
    <div class="mb-3">
  <label class="form-label">Kapanış Saati</label>
  <input type="time" class="form-control" name="kapanis_saati" required>
</div>

    <div class="mb-3">
      <label class="form-label">Konum - Enlem</label>
      <input type="text" class="form-control" name="latitude" placeholder="Örn: 41.0082" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Konum - Boylam</label>
      <input type="text" class="form-control" name="longitude" placeholder="Örn: 28.9784" required>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-custom">➕ Ekle</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
