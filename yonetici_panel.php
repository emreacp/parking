<?php
require 'db.php';
session_start();
$bugun = date('Y-m-d');

$istatistikler = [
  'toplamOtopark' => $pdo->query("SELECT COUNT(*) FROM otoparklar")->fetchColumn(),
  'toplamBos' => $pdo->query("SELECT SUM(bos_yer_sayisi) FROM otoparklar")->fetchColumn(),
  'toplamDolu' => $pdo->query("SELECT SUM(dolu_yer_sayisi) FROM otoparklar")->fetchColumn(),
  'bugunOtopark' => $pdo->query("SELECT COUNT(*) FROM otoparklar WHERE DATE(tarih) = '$bugun'")->fetchColumn(),
  'toplamKullanici' => $pdo->query("SELECT COUNT(*) FROM users ")->fetchColumn(),
  'bugunKullanici' => $pdo->query("SELECT COUNT(*) FROM users WHERE  DATE(kayit_tarihi) = '$bugun'")->fetchColumn()
];


if (!isset($_SESSION['kullanici_email']) || $_SESSION['kullanici_email'] !== 'emreacp@gmail.com') {
    header("Location: index.php");
    exit;
}
$email = $_SESSION['kullanici_email']; 
$onayli = $pdo->query("SELECT * FROM otoparklar WHERE onayli = 1")->fetchAll(PDO::FETCH_ASSOC);
$bekleyen = $pdo->query("SELECT * FROM otoparklar WHERE onayli = 0")->fetchAll(PDO::FETCH_ASSOC);
$adaylar = $pdo->query("SELECT * FROM users WHERE rol = 'aday_admin'")->fetchAll(PDO::FETCH_ASSOC);
$kullanicilar = $pdo->query("SELECT * FROM users ")->fetchAll(PDO::FETCH_ASSOC);
$bildirimler = $pdo->query("SELECT * FROM bildirimler ORDER BY zaman DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);


$gonderildi = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesaj'])) {
    $mesaj = trim($_POST['mesaj']);
    if (!empty($mesaj)) {
        $stmt = $pdo->prepare("INSERT INTO mesajlar (gonderen_email, mesaj) VALUES (?, ?)");
        $stmt->execute([$email, $mesaj]);
        $gonderildi = true; // Ekle bunu
    }
}


?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>ACP Yönetici Paneli</title>
  <link rel="stylesheet" href="style.css">
  <style>
     #mesajButonu {
      position: fixed;
      top: 200px;
      left: 10px;
      z-index: 1000;
      background-color: #ffc107;
      color: black;
      font-weight: bold;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
    }
    #mesajKutusu {
      position: fixed;
      top: 200px;
      left: 60px;
      width: 300px;
      display: none;
      background: #fff;
      border: 1px solid #ccc;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 15px;
      border-radius: 10px;
      z-index: 1001;
    }
    body { font-family: Arial; 
      background: #f4f4f4; 
      margin: 0; }
    .panel-container { max-width: 1100px;
       margin: 30px auto; padding: 20px; background: white; border-radius: 8px; 
       box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
    .tabs button { padding: 10px 20px; background:rgb(9, 9, 9); 
      color: white; border: none; 
      border-radius: 6px; cursor: pointer; }
    .tabs button.active { background:rgb(102, 247, 5); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    table { width: 100%;
       border-collapse: collapse;
        margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; 
      padding: 10px; text-align: left; }
    th { background: #eee; }
    .search-box { margin-bottom: 10px; }
    .search-box input { width: 100%; padding: 8px;
       border-radius: 6px; 
       border: 1px solid #ccc; }
   .edit-button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  background-color:rgb(9, 195, 251);
  border: none;
  border-radius: 6px;
  color: white;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.edit-button:hover {
  background-color:rgb(10, 249, 50);
}

    .card { background: #fafafa; 
      border: 1px solid #ddd; padding: 10px; 
      margin-bottom: 10px; border-radius: 6px; 
      box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .pagination { display: flex; justify-content: center;
       gap: 5px; flex-wrap: wrap; }
    .pagination button { padding: 6px 12px; border: none; 
      background: white; color: #007bff; 
      border-radius: 4px; cursor: pointer;
       min-width: 30px; border: 1px solid #007bff; }
    .pagination button.active { background: #007bff; color: white;
       font-weight: bold; }
   .modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5);
  overflow: auto;
  justify-content: center;
  align-items: center;
}
  .modal-content {
  background-color: #fff;
  margin: 50px auto;
  padding: 30px 40px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease;
  position: relative;
}
.modal-content h3 {
  margin-bottom: 20px;
  font-size: 22px;
  color: #333;
  text-align: center;
}
    .close-btn {
  position: absolute;
  top: 12px;
  right: 16px;
  font-size: 24px;
  color: #555;
  cursor: pointer;
  transition: color 0.2s;
}
.close-btn:hover {
  color: #000;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 6px;
  color: #444;
}
.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
}
.delete-form {
  margin-top: 15px;
}

    .message { margin-top: 10px; font-weight: bold; }
    .dosya-btn { background: #17a2b8; color: white;
       padding: 6px 12px; border: none; 
       border-radius: 4px; text-decoration: none; }
       margin-bottom: 12px;
        
     
        .counter {
  font-size: 28px;
  color:rgb(7, 7, 7);
}

.stat-card-animated {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  padding: 20px;
  text-align: center;
  flex: 1 1 calc(33.3% - 10px);
  opacity: 0;
  transform: translateY(20px);
  animation: fadeUp 0.6s ease forwards;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s;
}

.stat-card-animated:hover {
  transform: scale(1.03);
}

.stat-card-animated h3 {
  font-size: 28px;
  color:rgb(172, 48, 48);
  margin-bottom: 5px;
}

.stat-card-animated p {
  font-size: 14px;
  color: #333;
  margin-bottom: 10px;
}

@keyframes fadeUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.progress-bar {
  width: 100%;
  height: 5px;
  background: #eee;
  border-radius: 50px;
  overflow: hidden;
  position: relative;
}

.progress-bar .fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg,rgb(215, 243, 6), #00bfff);
  transition: width 1.2s ease-out;
}

  </style>
</head>
<!-- 🔔 BİLDİRİM BUTONU VE PANELİ -->
<div id="bildirim-btn" onclick="toggleBildirimKutusu()" style="
  position: fixed; bottom: 20px; right: 20px;
  background-color: #007bff; color: white; border-radius: 50%;
  width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer; z-index: 999; transition: transform 0.2s ease-in-out;">
  🔔
  <span id="bildirim-sayi" style="
    position: absolute;
    top: 5px;
    right: 5px;
    background: red;
    color: white;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: none;
    align-items: center;
    justify-content: center;
  "></span>
</div>

<div id="bildirim-kutusu" style="
  position: fixed; bottom: 80px; right: 20px;
  width: 300px; max-height: 400px; overflow-y: auto;
  background: white; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  padding: 15px; display: none; z-index: 998;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.3s ease;
">
  <h4 style="margin-top:0; color:#007bff; display: flex; justify-content: space-between; align-items: center;">
    Son Bildirimler
    <button onclick="kapatBildirimKutusu()" style="background:none; border:none; font-size: 20px; cursor:pointer; color: #888;">&times;</button>
  </h4>
  <?php if (count($bildirimler) === 0): ?>
    <p style="color: #888;">Bildirim yok</p>
  <?php else: ?>
    <?php foreach ($bildirimler as $b): ?>
      <div style="border-left: 3px solid #007bff; padding-left: 10px; margin-bottom: 10px; background:#f9f9f9; border-radius:6px; padding:10px;">
        <strong><?= htmlspecialchars($b['ad']) ?></strong><br>
        <small><?= htmlspecialchars($b['guncelleyen']) ?> → <?= htmlspecialchars($b['degisiklik']) ?></small><br>
        <span style="font-size:12px; color:#999;"><?= date('d.m.Y H:i', strtotime($b['zaman'])) ?></span>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
  let kutuAcik = false;

  function toggleBildirimKutusu() {
    const kutu = document.getElementById("bildirim-kutusu");
    const sayi = document.getElementById("bildirim-sayi");
    const buton = document.getElementById("bildirim-btn");
    kutuAcik = !kutuAcik;

    if (kutuAcik) {
      kutu.style.display = "block";
      setTimeout(() => {
        kutu.style.opacity = "1";
        kutu.style.transform = "translateY(0)";
      }, 10);
      sayi.style.display = "none";
    } else {
      kapatBildirimKutusu();
    }
  }

  function kapatBildirimKutusu() {
    const kutu = document.getElementById("bildirim-kutusu");
    kutu.style.opacity = "0";
    kutu.style.transform = "translateY(20px)";
    setTimeout(() => {
      kutu.style.display = "none";
    }, 300);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const sayi = document.getElementById("bildirim-sayi");
    const buton = document.getElementById("bildirim-btn");
    const bildirimAdedi = <?= count($bildirimler) ?>;
    if (bildirimAdedi > 0) {
      sayi.innerText = bildirimAdedi > 9 ? '9+' : bildirimAdedi;
      sayi.style.display = "flex";

      // Titretme efekti (shake)
      buton.style.animation = "shake 0.5s infinite alternate";
      setTimeout(() => { buton.style.animation = ""; }, 3000);
    }
  });

  // 🔁 Shake animasyonu
  const style = document.createElement('style');
  style.innerHTML = `
    @keyframes shake {
      0% { transform: rotate(0deg); }
      25% { transform: rotate(5deg); }
      50% { transform: rotate(-5deg); }
      75% { transform: rotate(4deg); }
      100% { transform: rotate(0deg); }
    }
  `;
  document.head.appendChild(style);
</script>

<body>
  <?php include 'navbar.php'; ?>
  <div class="panel-container">
  <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    <?php if (isset($gonderildi) && $gonderildi): ?>
 
<?php endif; ?>

<!-- Sabit Mesaj Butonu -->
<div id="mesajButonu" onclick="toggleMesajKutusu()">✉️ Mesaj Gönder</div>
<!-- Mesaj Kutusu -->
<div id="mesajKutusu">
  <div style="position: relative;">
  <button type="button" onclick="closeMesajKutusu()" class="btn-close" style="position: absolute; top: 0; right: 0;"></button>
</div>

  <form method="POST">
    <div class="mb-2">
      <textarea name="mesaj" class="form-control" required placeholder="Tüm adminlere mesaj yaz..." rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-warning btn-sm w-100">Gönder</button>
  </form>
</div>

<script>
  function closeMesajKutusu() {
  document.getElementById("mesajKutusu").style.display = "none";
}

  function toggleMesajKutusu() {
    const kutu = document.getElementById("mesajKutusu");
    kutu.style.display = kutu.style.display === "none" ? "block" : "none";
  }
  
</script>

  <?php
  
    $kartlar = [
      'Toplam Otopark' => $istatistikler['toplamOtopark'],
      'Boş Yer Sayısı' => $istatistikler['toplamBos'],
      'Dolu Yer Sayısı' => $istatistikler['toplamDolu'],
      'Bugün Eklenen Otopark' => $istatistikler['bugunOtopark'],
      'Toplam Kullanıcı' => $istatistikler['toplamKullanici'],
      'Bugünkü Kullanıcı' => $istatistikler['bugunKullanici']
    ];
    foreach ($kartlar as $baslik => $deger):
  ?>
   <div class="stat-card-animated ">
      <h3 class="counter" data-count="<?= $deger ?>">0</h3>
      <p><?= $baslik ?></p>
      <div class="progress-bar"><div class="fill"></div></div>
    </div>
  <?php endforeach; ?>
</div>
  
    
    <div class="tabs">
      <button id="otoparklar-btn" class="active" onclick="openTab('otoparklar')">Otoparklarımız</button>
      <button id="bekleyen-btn" onclick="openTab('bekleyen')">Onay Bekleyen Otoparklar</button>
      <button id="adaylar-btn" onclick="openTab('adaylar')">İşletmeci Adayları</button>
       <button id="kullanicilar-btn" onclick="openTab('kullanicilar')">Kullanıcılar</button>
    </div>

    <div id="otoparklar" class="tab-content active">
      <div class="search-box">
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Otopark ismi ile ara...">
      </div>
      <table id="otoparkTable">
        <thead>
          <tr><th>Ad</th><th>İl</th><th>Ücret</th><th>Dolu</th><th>Boş</th><th>İşlem</th></tr>
        </thead>
        <tbody>
          <?php foreach($onayli as $o): ?>
          <tr data-id="<?= $o['id'] ?>">
            <td><?= htmlspecialchars($o['ad']) ?></td>
            <td><?= htmlspecialchars($o['il']) ?></td>
            <td><?= htmlspecialchars($o['ucret']) ?> ₺</td>
            <td><?= $o['dolu_yer_sayisi'] ?></td>
            <td><?= $o['bos_yer_sayisi'] ?></td>
            <td><button class="edit-button" onclick='openEditModal(<?= json_encode($o) ?>)'>Düzenle</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="pagination" id="pagination"></div>
    </div>

    <div id="bekleyen" class="tab-content">
  <div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php foreach($bekleyen as $b): ?>
    <div style="flex: 1 1 calc(33.3% - 20px); background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; min-width: 280px;">
      <h4 style="margin-bottom: 10px; color: #007bff;"><?= htmlspecialchars($b['ad']) ?></h4>
      <p><strong>İl:</strong> <?= htmlspecialchars($b['il']) ?></p>
      <p><strong>Ücret:</strong> <?= $b['ucret'] ?> ₺</p>
      <p><strong>Dolu:</strong> <?= $b['dolu_yer_sayisi'] ?> - <strong>Boş:</strong> <?= $b['bos_yer_sayisi'] ?></p>
      <a class="edit-button" href="otopark_onayla.php?id=<?= $b['id'] ?>">Onayla</a>
      <a class="edit-button" style="background:#dc3545;" href="otopark_onayla.php?reddet=<?= $b['id'] ?>">Reddet</a>
    </div>
    <?php endforeach; ?>
  </div>
</div>


   <div id="adaylar" class="tab-content">
  <div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php foreach($adaylar as $a): ?>
    <div style="flex: 1 1 calc(33.3% - 20px); background: url('img/arka.png') center/cover no-repeat, #f7f7f7; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; color: #333; min-width: 280px;">
      <h4 style="color: #333; margin-bottom: 10px;"> <?= htmlspecialchars($a['ad']) ?></h4>
      <p><strong>Email:</strong> <?= htmlspecialchars($a['email']) ?></p>
      <?php if (!empty($a['belge_yolu'])): ?>
      <a href="<?= $a['belge_yolu'] ?>" target="_blank" class="dosya-btn" style="margin-bottom: 10px; display: inline-block;">Dosyayı Görüntüle</a>
      <?php else: ?>
      <span class="text-muted">Dosya yüklenmemiş</span><br>
      <?php endif; ?>
      <a class="edit-button" href="aday_islem.php?id=<?= $a['id'] ?>">Onayla</a>
      <a class="edit-button" style="background:#dc3545;" href="aday_islem.php?reddet=<?= urlencode($a['email']) ?>">Reddet</a>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div id="kullanicilar" class="tab-content">
  <h2>Kullanıcılar</h2>
  <table>
    <thead>
      <tr><th>Ad</th><th>Email</th><th>Kayıt Tarihi</th></tr>
    </thead>
    <tbody>
      <?php foreach($kullanicilar as $k): ?>
        <tr>
          <td><?= htmlspecialchars($k['ad']) ?></td>
          <td><?= htmlspecialchars($k['email']) ?></td>
          <td><?= $k['kayit_tarihi'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeEditModal()">&times;</span>
    <h3>Otopark Bilgilerini Düzenle</h3>
    <form id="editForm">
      <input type="hidden" name="id" id="modal-id">

      <div class="form-group">
        <label for="modal-ad">Ad:</label>
        <input type="text" name="ad" id="modal-ad" required>
      </div>

      <div class="form-group">
        <label for="modal-il">İl:</label>
        <input type="text" name="il" id="modal-il" required>
      </div>

      <div class="form-group">
        <label for="modal-ucret">Ücret:</label>
        <input type="number" name="ucret" id="modal-ucret" required>
      </div>

      <div class="form-group">
        <label for="modal-dolu">Dolu Yer Sayısı:</label>
        <input type="number" name="dolu" id="modal-dolu" required>
      </div>

      <div class="form-group">
        <label for="modal-bos">Boş Yer Sayısı:</label>
        <input type="number" name="bos" id="modal-bos" required>
      </div>

      <button type="submit" class="edit-button">Kaydet</button>
      <div class="message" id="form-message"></div>
    </form>

    <form method="POST" action="otopark_sil.php" onsubmit="return confirm('Bu otopark silinsin mi?');" class="delete-form">
      <input type="hidden" name="id" id="modal-sil-id">
      <button type="submit" class="edit-button" style="background: #dc3545">Sil</button>
    </form>
  </div>
</div>

  <script>
    function openTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.tabs button').forEach(btn => btn.classList.remove('active'));
      document.getElementById(tabId).classList.add('active');
      document.getElementById(tabId + '-btn').classList.add('active');
    }

    function filterTable() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const rows = document.querySelectorAll("#otoparkTable tbody tr");

  rows.forEach(row => {
    const matches = row.textContent.toLowerCase().includes(input);
    row.style.display = matches ? "" : "none";
    row.classList.toggle("filtered-out", !matches); // işaretleme
  });

  displayTablePage(1);
}

  function openEditModal(data) {
    document.getElementById('modal-id').value = data.id;
    document.getElementById('modal-sil-id').value = data.id;
    document.getElementById('modal-ad').value = data.ad;
    document.getElementById('modal-il').value = data.il;
    document.getElementById('modal-ucret').value = data.ucret;
    document.getElementById('modal-dolu').value = data.dolu_yer_sayisi;
    document.getElementById('modal-bos').value = data.bos_yer_sayisi;
    document.getElementById('form-message').textContent = "";
    document.getElementById('editModal').style.display = 'flex';
  }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById("editForm").addEventListener("submit", function(e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      fetch("otopark_guncelle.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        const msg = document.getElementById("form-message");
        if (data.success) {
          msg.textContent = "Güncelleme başarılı.";
          msg.style.color = "green";
          setTimeout(() => {
            closeEditModal();
            location.reload();
          }, 1000);
        } else {
          msg.textContent = data.message || "Hata oluştu.";
          msg.style.color = "red";
        }
      })
      .catch(() => {
        const msg = document.getElementById("form-message");
        msg.textContent = "İstek gönderilemedi.";
        msg.style.color = "red";
      });
    });

     const rowsPerPage = 10;
  let currentPage = 1;

  function displayTablePage(page) {
  const allRows = document.querySelectorAll("#otoparkTable tbody tr:not(.filtered-out)");

  const visibleRows = Array.from(allRows);
  const totalRows = visibleRows.length;

  const rowsPerPage = 10;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  currentPage = Math.max(1, Math.min(page, totalPages)); // sınırları aşmasın

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  visibleRows.forEach((row, index) => {
    row.style.display = (index >= start && index < end) ? "" : "none";
  });

  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = startPage + maxVisiblePages - 1;
  if (endPage > totalPages) {
    endPage = totalPages;
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  // İlk Sayfa
  if (currentPage > 1) {
    const firstBtn = document.createElement("button");
    firstBtn.innerHTML = "«";
    firstBtn.onclick = () => displayTablePage(1);
    pagination.appendChild(firstBtn);

    const prevBtn = document.createElement("button");
    prevBtn.innerHTML = "<";
    prevBtn.onclick = () => displayTablePage(currentPage - 1);
    pagination.appendChild(prevBtn);
  }

  // Sayfa Numaraları
  for (let i = startPage; i <= endPage; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");
    btn.onclick = () => displayTablePage(i);
    pagination.appendChild(btn);
  }

  // Son Sayfa
  if (currentPage < totalPages) {
    const nextBtn = document.createElement("button");
    nextBtn.innerHTML = ">";
    nextBtn.onclick = () => displayTablePage(currentPage + 1);
    pagination.appendChild(nextBtn);

    const lastBtn = document.createElement("button");
    lastBtn.innerHTML = "»";
    lastBtn.onclick = () => displayTablePage(totalPages);
    pagination.appendChild(lastBtn);
  }
}


  // Sayfa yüklendiğinde 1. sayfayı göster
  window.addEventListener("DOMContentLoaded", () => {
    displayTablePage(1);
  });

    window.onload = () => displayTablePage(currentPage);
    document.querySelectorAll('.counter').forEach(counter => {
  const updateCount = () => {
    const target = +counter.getAttribute('data-count');
    const count = +counter.innerText;
    const increment = Math.ceil(target / 50);

    if (count < target) {
      counter.innerText = count + increment;
      setTimeout(updateCount, 20);
    } else {
      counter.innerText = target;
    }
  };
  updateCount();
});
document.querySelectorAll('.counter').forEach(counter => {
  const target = +counter.getAttribute('data-count');
  const fillBar = counter.parentElement.querySelector('.fill');
  let count = 0;
  const increment = Math.ceil(target / 50);

  const update = () => {
    if (count < target) {
      count += increment;
      counter.innerText = count;
      const percent = Math.min(100, (count / target) * 100);
      if (fillBar) fillBar.style.width = percent + '%';
      setTimeout(update, 20);
    } else {
      counter.innerText = target;
      if (fillBar) fillBar.style.width = '100%';
    }
  };
  update();
});

  </script>
</body>
</html>
