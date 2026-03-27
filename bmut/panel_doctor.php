<?php 
	session_start();
	$ad = $_SESSION['ad'];
	$soyad = $_SESSION['soyad'];

	echo "Hoşgeldiniz, " . $ad . " " . $soyad;

	unset($_SESSION['ad']);
	unset($_SESSION['soyad']);
?>