<?php 

	session_start();
	require_once "db_baglanti.php";

	$role = $_POST['role'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$password = $_POST['password']; 

	if ($role === 'patient') 
	{
		$query = $db->prepare("SELECT * FROM hastalar WHERE email = ?");
		$query->execute([$email]);
		$kullanici = $query->fetch();

		if ($kullanici && password_verify($password, $kullanici['sifre'])) {
			$_SESSION['ad'] = $kullanici['h_ad'];
			$_SESSION['soyad'] = $kullanici['h_soyad'];
			header("Location: panel_patient.php");
			exit;
		}
		else
		{
			$_SESSION['hata'] = "Hatalı email veya şifre!";
			header("Location: login.php");
			exit;
		}
		
	}
	elseif ($role === 'doctor') 
	{
		$query = $db->prepare("SELECT * FROM doktorlar WHERE email = ?");
		$query->execute([$email]);
		$kullanici = $query->fetch();

		if ($kullanici && password_verify($password, $kullanici['sifre'])) {
			$_SESSION['ad'] = $kullanici['d_ad'];
			$_SESSION['soyad'] = $kullanici['d_soyad'];
			header("Location: panel_doctor.php");
			exit;
		}
		else
		{
			$_SESSION['hata'] = "Hatalı email veya şifre!";
			header("Location: login.php");
			exit;
		}
	}
	else
	{
		$query = $db->prepare("SELECT * FROM yoneticiler WHERE email = ?");
		$query->execute([$email]);
		$kullanici = $query->fetch();

		if ($kullanici && password_verify($password, $kullanici['sifre'])) {
			$_SESSION['ad'] = $kullanici['a_ad'];
			$_SESSION['soyad'] = $kullanici['a_soyad'];
			header("Location: panel_admin.php");
			exit;
		}
		else
		{
			$_SESSION['hata'] = "Hatalı email veya şifre!";
			header("Location: login.php");
			exit;
		}
	}
 ?>