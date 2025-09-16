<?php
session_start();
require 'db.php';

if (!isset($_SESSION['kullanici_email'])) {
    header("Location: giris.php");
    exit();
}

$email = $_SESSION['kullanici_email'];

// Kullanıcının admin olup olmadığını kontrol et
$stmt = $pdo->prepare("SELECT rol FROM users WHERE email = ?");
$stmt->execute([$email]);
$kullanici = $stmt->fetch();

if (!$kullanici || $kullanici['rol'] !== 'admin') {
    echo "Bu sayfaya sadece adminler erişebilir.";
    exit();
}

// Adminin sahip olduğu otoparkları getir
$stmt2 = $pdo->prepare("SELECT * FROM otoparklar WHERE sahip_email = ?");
$stmt2->execute([$email]);
$otoparklar = $stmt2->fetchAll();
$mesajlar = $pdo->query("SELECT * FROM mesajlar ORDER BY tarih DESC LIMIT 3")->fetchAll();

// Tüm şehirleri al
$sehirler = $pdo->query("SELECT DISTINCT il FROM otoparklar")->fetchAll(PDO::FETCH_COLUMN);

// İstatistik için şehir seçildiyse işle
$secilen_il = $_GET['il'] ?? null;
$istatistik = null;
if ($secilen_il) {
    $stmt3 = $pdo->prepare("SELECT COUNT(*) as sayi, AVG(ucret) as ort_ucret, AVG(bos_yer_sayisi) as ort_bos, AVG(dolu_yer_sayisi) as ort_dolu FROM otoparklar WHERE il = ?");
    $stmt3->execute([$secilen_il]);
    $istatistik = $stmt3->fetch();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Paneli</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
  .btn-goster {
    background: linear-gradient(to right, #00b894, #00cec9);
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    padding: 5px 14px;
    font-size: 14px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }
  .btn-goster:hover {
    background: linear-gradient(to right, #00cec9, #00b894);
    transform: scale(1.05);
  }
</style>

</head>
<body>

<?php include 'navbar.php'; ?>
<div id="guncelleMesaji" class="alert d-none text-center fw-bold position-fixed top-0 start-50 translate-middle-x mt-3 px-4" style="z-index: 1056; max-width: 400px;"></div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
  <?php foreach ($mesajlar as $m): ?>
    <div class="toast align-items-center text-bg-info border-0 mb-2 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <strong><?= htmlspecialchars($m['gonderen_email']) ?></strong>: <?= htmlspecialchars($m['mesaj']) ?>
          <div class="small text-light"><?= date('d.m.Y H:i', strtotime($m['tarih'])) ?></div>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="container py-4">
  <h2 class="mb-4">Otoparklarım</h2>

  <div class="container py-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">📊 Şehir Bazlı Otopark İstatistikleri</h5>
      <form method="GET" class="d-flex align-items-center gap-3">
        <select name="il" class="form-select w-auto" style="min-width: 200px;">
          <option value="">Şehir Seçiniz</option>
          <?php foreach ($sehirler as $sehir): ?>
            <option value="<?= $sehir ?>" <?= ($sehir === $secilen_il ? 'selected' : '') ?>><?= $sehir ?></option>
          <?php endforeach; ?>
        </select>
      <button type="submit" style="width: 200px;" class="btn-goster d-flex align-items-center gap-1">
  <span>📊</span> Göster
</button>

      </form>

      <?php if ($istatistik): ?>
        <div class="table-responsive mt-4">
          <table class="table table-bordered text-center align-middle shadow-sm table-striped table-hover">


            <thead class="table-success">

              <tr>
                <th>Şehir</th>
                <th>Otopark Sayısı</th>
                <th>Ortalama Ücret (₺)</th>
                <th>Ortalama Boş Yer</th>
                <th>Ortalama Dolu Yer</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= htmlspecialchars($secilen_il) ?></td>
                <td><?= $istatistik['sayi'] ?></td>
                <td class="<?= $istatistik['ort_ucret'] > 20 ? 'table-warning' : '' ?>">
  <?= number_format($istatistik['ort_ucret'], 2) ?>
</td>
                <td class="<?= $istatistik['ort_bos'] > 20 ? 'table-warning' : '' ?>">
  <?= number_format($istatistik['ort_bos'], 2) ?>
</td>
                <td class="<?= $istatistik['ort_dolu'] > 50 ? 'table-warning' : '' ?>">
  <?= number_format($istatistik['ort_dolu'], 2) ?>
</td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

  <div class="row">
    <?php foreach ($otoparklar as $otopark): ?>
     <div class="col-md-4 mb-4">
  <div class="card shadow-lg border-0 rounded-4 h-100">
    <div class="card-header fw-bold" style="background-color: #e0f7fa; color: #000; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">

      <?= htmlspecialchars($otopark['ad']) ?>
    </div>
    <div class="card-body">
      
      <p><strong>📍 İl:</strong> <?= htmlspecialchars($otopark['il']) ?></p>
      <p><strong>💰 Ücret:</strong> <?= $otopark['ucret'] ?> ₺</p>
      <p><strong>🚗 Dolu:</strong> <?= $otopark['dolu_yer_sayisi'] ?> | <strong>🅿️ Boş:</strong> <?= $otopark['bos_yer_sayisi'] ?></p>
      <p><strong>⏱️ Kapanış:</strong> <?= $otopark['kapanis_saati'] ?></p>
      <p>
        <span class="badge <?= $otopark['onayli'] ? 'bg-success' : 'bg-warning text-dark' ?>">
          <?= $otopark['onayli'] ? 'Onaylı' : 'Onay Bekliyor' ?>
        </span>
      </p>
      <button class="btn btn-outline-primary btn-sm mt-2" 
              data-bs-toggle="modal" 
              data-bs-target="#editModal"
              data-id="<?= $otopark['id'] ?>"
              data-ad="<?= htmlspecialchars($otopark['ad']) ?>"
              data-ucret="<?= $otopark['ucret'] ?>"
              data-dolu="<?= $otopark['dolu_yer_sayisi'] ?>"
              data-bos="<?= $otopark['bos_yer_sayisi'] ?>">
        🛠️ Düzenle
      </button>
    </div>
  </div>
</div>

    <?php endforeach; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm">

        <div class="modal-header">
          <h5 class="modal-title">Otoparkı Güncelle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="id" id="modal-id">

          <div class="mb-3">
            <label>Otopark Adı</label>
            <input type="text" name="ad" id="modal-ad" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Şehir</label>
            <input type="text" name="il" id="modal-il" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Ücret</label>
            <input type="number" name="ucret" id="modal-ucret" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Dolu Yer</label>
            <input type="number" name="dolu" id="modal-dolu" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Boş Yer</label>
            <input type="number" name="bos" id="modal-bos" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  const editModal = document.getElementById('editModal');
  editModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('modal-id').value = button.getAttribute('data-id');
    document.getElementById('modal-ad').value = button.getAttribute('data-ad');     
    document.getElementById('modal-il').value = button.getAttribute('data-il');      
    document.getElementById('modal-ucret').value = button.getAttribute('data-ucret');
    document.getElementById('modal-dolu').value = button.getAttribute('data-dolu');
    document.getElementById('modal-bos').value = button.getAttribute('data-bos');
  });
   document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('otopark_guncelle.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      const mesajKutusu = document.getElementById('guncelleMesaji');

      if (data.success) {
        mesajKutusu.textContent = '✅ Güncelleme başarılı';
        mesajKutusu.className = 'alert alert-success text-center fw-bold position-fixed top-0 start-50 translate-middle-x mt-3 px-4';
      } else {
        mesajKutusu.textContent = '❌ Güncelleme başarısız';
        mesajKutusu.className = 'alert alert-danger text-center fw-bold position-fixed top-0 start-50 translate-middle-x mt-3 px-4';
      }

      mesajKutusu.classList.remove('d-none');

      setTimeout(() => {
        mesajKutusu.classList.add('d-none');
      }, 3000);

      const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
      modal.hide();
    });
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.toast').forEach(toastEl => {
      const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
      toast.show();
    });
  });
</script>

</body>
</html>
