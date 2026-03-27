<?php
session_start();
include "db_baglanti.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(!isset($_SESSION['email'])){
    die("Oturum bulunamadı.");
}

$verification_code = rand(100000,999999);

$_SESSION['code'] = $verification_code;
$_SESSION['code_time'] = time(); // süreyi sıfırla

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hastane.randevu.sistemii@gmail.com';
    $mail->Password = 'tchp icjy jgvy hhdf';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('hastane.randevu.sistemii@gmail.com', 'Hastane Sistemi');
    $mail->addAddress($_SESSION['email']);

    $mail->Subject = 'Yeni Doğrulama Kodunuz';
    $mail->Body = "Yeni doğrulama kodunuz: " . $verification_code;

    $mail->send();

    header("Location: verify_email.php");
    exit;

} catch (Exception $e) {
    echo "Mail gönderilemedi: {$mail->ErrorInfo}";
}
?>