<?php 
	session_start();
	require_once "db_baglanti.php";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (time() - $_SESSION['code_time'] > 180) {
			echo "Kodun süresi doldu, yeni kod isteyin!";
			exit;
		}


		if (isset($_POST['code']) &&  $_POST['code'] == $_SESSION['code']) {
			$stmt  = $db->prepare("INSERT INTO hastalar(h_ad, h_soyad, cinsiyet, dogum_tarihi, email, sifre ) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->execute([$_SESSION['name'],
							$_SESSION['surname'],
							$_SESSION['gender'],
							$_SESSION['bdate'],
							$_SESSION['email'],
							$_SESSION['password']]);

		session_unset();
		session_destroy();

		echo "Kayıt Başarılı!";
		}

		else
		{
			echo "Kod Doğrulanamadı!";
		}
	}
	else
	{
		echo "Geçersiz istek!";
	}

?>
