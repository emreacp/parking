<?php
require 'db.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tip = $_POST['giris_tipi'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    if ($tip === "kayit") {
        $ad = $_POST['ad'];
        $rol = $_POST['rol'];
        $belge_yolu = null;

        if ($rol === "aday_admin" && isset($_FILES['belge']) && $_FILES['belge']['error'] === 0) {
            $dosyaAdi = uniqid() . "_" . basename($_FILES['belge']['name']);
            $hedefYol = "belgeler/" . $dosyaAdi;

            if (!file_exists("belgeler")) mkdir("belgeler", 0777, true);
            move_uploaded_file($_FILES['belge']['tmp_name'], $hedefYol);
            $belge_yolu = $hedefYol;
        }

        $sifre_hashed = password_hash($sifre, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (ad, email, sifre, rol, belge_yolu, dogrulandi) VALUES (?, ?, ?, ?, ?, 1)");

        try {
            $stmt->execute([$ad, $email, $sifre_hashed, $rol, $belge_yolu]);

            if ($rol === "kullanici") {
                echo "<script>
                    localStorage.setItem('kayitBasarili', 'true');
                    window.location.href = 'giris_kayit.html#register';
                </script>";
            } else {
                echo "<script>
                    alert('✅ Başvurunuz alınmıştır. Yönetici onayını bekleyin.');
                    window.location.href = 'index.php';
                </script>";
            }
        } catch (PDOException $e) {
            echo "❌ Kayıt başarısız: " . $e->getMessage();
        }
    } elseif ($tip === "kullanici" || $tip === "admin") {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $kullanici = $stmt->fetch();

        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            $_SESSION['kullanici_email'] = $email;
            $_SESSION['rol'] = $kullanici['rol'];

            if ($_SESSION['rol'] === 'yonetici') {
                 $_SESSION['mesaj_goster'] = true;
                header("Location: yonetici_panel.php");
            } elseif ($_SESSION['rol'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: harita.php");
            }
            exit;
        } else {
             echo "<script>
      window.location.href = 'giris_kayit.html#error=gecersiz';
    </script>";
    exit;
        }
    }
}
?>
