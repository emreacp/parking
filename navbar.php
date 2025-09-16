<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

$ad = null;
$rol = null;

if (isset($_SESSION['kullanici_email'])) {
    $email = $_SESSION['kullanici_email'];
    $stmt = $pdo->prepare("SELECT ad, rol FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user) {
        $ad = $user['ad'];
        $rol = $user['rol'];
    }
}
?>

<div class="navbar">
  <div class="logo">
    <a href="index.php"><img src="img/logo.jpg" alt="Logo"></a>
  </div>
  <a href="index.php">Ana Sayfa</a>
  <a href="iletisim.php">İletişim</a>

  <div style="margin-left:auto; display:flex; align-items:center; gap: 10px;">
    <?php if (!isset($_SESSION['kullanici_email'])): ?>
      <div class="dropdown">
        <button onclick="document.getElementById('loginDropdown').classList.toggle('show')">Giriş ⮟</button>
        <div class="dropdown-content" id="loginDropdown">
          <a href="giris_kayit.html#userLogin">Kullanıcı Girişi/Kayıt Ol</a>
          <a href="giris_kayit.html#adminLogin">Admin Girişi</a>
        </div>
      </div>
    <?php else: ?>
      <?php if ($_SESSION['kullanici_email'] === 'emreacp@gmail.com'): ?>
        <a href="yonetici_panel.php">Yönetici Paneli</a>
        <a href="harita.php">Harita</a>
      <?php elseif ($rol === 'admin'): ?>
        <a href="admin_panel.php">Admin Paneli</a>
        <a href="otopark_ekle.php">Yeni Ekle</a>
        
      <?php endif; ?>

<span style="background: linear-gradient(to right,rgb(0, 0, 0),rgb(255, 0, 0)); color: white; padding: 8px 16px; border-radius: 25px; font-weight: bold; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
  Hoşgeldiniz, <?= htmlspecialchars($ad) ?>
</span>



      <a href="logout.php">Çıkış Yap</a>
    <?php endif; ?>
  </div>
</div>

<script>
  window.onclick = function(event) {
    if (!event.target.matches('.dropdown button')) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      for (var i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove('show');
      }
    }
  };
</script>
