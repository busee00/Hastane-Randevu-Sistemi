<?php 
	session_start();
	require_once "db_baglanti.php";

	$is_success = false;
	$message = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Süre kontrolü
		if (time() - $_SESSION['code_time'] > 180) {
			$message = "Kodun süresi doldu, lütfen tekrar kayıt olun!";
		}
		// Kod kontrolü
		else if (isset($_POST['code']) && $_POST['code'] == $_SESSION['code']) {
			$stmt = $db->prepare("INSERT INTO hastalar(h_ad, h_soyad, cinsiyet, dogum_tarihi, email, sifre ) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->execute([
				$_SESSION['name'],
				$_SESSION['surname'],
				$_SESSION['gender'],
				$_SESSION['bdate'],
				$_SESSION['email'],
				$_SESSION['password']
			]);

			$is_success = true;
			$message = "Kayıt işleminiz başarıyla tamamlandı. Artık randevu alabilirsiniz!";
			
			// İşlem bittiği için session temizliği
			session_unset();
			session_destroy();
		}
		else {
			$message = "Girdiğiniz kod hatalı. Lütfen kontrol edip tekrar deneyin.";
		}
	} else {
		$message = "Geçersiz erişim isteği!";
	}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>İşlem Sonucu</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style_register.css">
</head>
<body class="register">
	<div class="sonuc-kart">
		<?php if ($is_success): ?>
			<span class="sonuc-ikon basari-rengi">✓</span>
			<h2>Kayıt Başarılı!</h2>
			<p><?php echo $message; ?></p>
			<a href="login.php" class="btn-islem">Giriş Yap</a>
		<?php else: ?>
			<span class="sonuc-ikon hata-rengi">✕</span>
			<h2>Hata Oluştu</h2>
			<p><?php echo $message; ?></p>
			<a href="javascript:history.back()" class="btn-islem btn-geri">Geri Dön</a>
		<?php endif; ?>
	</div>
</body>
</html>