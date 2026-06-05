<?php
session_start();
session_destroy(); // Tüm oturum verilerini siler
header("Location: anasayfa.php"); // Ana sayfaya (login ekranına) atar
exit();