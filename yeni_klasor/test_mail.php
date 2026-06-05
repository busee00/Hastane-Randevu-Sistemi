<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'buse.inanc04@gmail.com';      // 👈 değiştir
    $mail->Password = 'ksms giku xqsk olkr';          // 👈 16 haneli şifre

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('GMAIL_ADRESIN@gmail.com', 'Test Sistemi');
    $mail->addAddress('buse.inanc04@gmail.com'); // 👈 kendi mailini yaz

    $mail->Subject = 'Test Mail';
    $mail->Body    = 'PHPMailer çalışıyor 🎉';

    $mail->send();

    echo 'Mail başarıyla gönderildi!';

} catch (Exception $e) {
    echo "Mail gönderilemedi. Hata: {$mail->ErrorInfo}";
}