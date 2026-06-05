<?php 
	try {
    $db = new PDO("mysql:host=localhost; dbname=hastane_randevu_sistemi;charset=utf8", "root", "12345678");
} catch (Exception $e) {
    die("Veritabanı bağlantısı sağlanamadı: " . $e->getMessage());
}
?>