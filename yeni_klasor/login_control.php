<?php 
session_start();
require_once "db_baglanti.php";

// Formdan gelen verileri kontrol et
if (!isset($_POST['role']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    $_SESSION['hata'] = "Lütfen tüm alanları doldurun!";
    header("Location: login.php");
    exit;
}

$role = $_POST['role'];
$email = $_POST['email'];
$password = $_POST['password'];

if ($role === 'patient') {
    $query = $db->prepare("SELECT * FROM hastalar WHERE email = ?");
    $query->execute([$email]);
    $kullanici = $query->fetch();

    if ($kullanici && password_verify($password, $kullanici['sifre'])) {
        $_SESSION['user_id'] = $kullanici['h_id'];
        $_SESSION['ad'] = $kullanici['h_ad'];
        $_SESSION['soyad'] = $kullanici['h_soyad'];
        $_SESSION['role'] = 'patient';
        header("Location: hasta_randevular.php");
        exit;
    }
} 
elseif ($role === 'doctor') {
    $query = $db->prepare("SELECT * FROM doktorlar WHERE e_mail = ?");
    $query->execute([$email]);
    $kullanici = $query->fetch();

    if ($kullanici && password_verify($password, $kullanici['sifre'])) {
        $_SESSION['doctor_id'] = $kullanici['d_id'];
        $_SESSION['ad'] = $kullanici['d_ad'];
        $_SESSION['soyad'] = $kullanici['d_soyad'];
        $_SESSION['unvan'] = $kullanici['unvan'];
        $_SESSION['role'] = 'doctor';
        header("Location: doktor_randevular.php");
        exit;
    }
} 

elseif ($role === 'admin') {
    $query = $db->prepare("SELECT * FROM yoneticiler WHERE email = ?");
    $query->execute([$email]);
    $kullanici = $query->fetch();

    if ($kullanici && password_verify($password, $kullanici['sifre'])) {
        $_SESSION['admin_id'] = $kullanici['a_id'];
        $_SESSION['ad'] = $kullanici['a_ad'];
        $_SESSION['soyad'] = $kullanici['a_soyad'];
        $_SESSION['role'] = 'admin';
        header("Location: admin_randevular.php");
        exit;
    }
}

// Eğer yukarıdaki if'lerden hiçbirine girip exit yapmadıysa giriş hatalıdır
$_SESSION['hata'] = "Hatalı email, şifre veya rol seçimi!";
header("Location: login.php");
exit;
?>