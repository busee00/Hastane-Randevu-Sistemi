<?php
	session_start();
	require_once "db_baglanti.php";

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$email = $_POST['email'];

		$query = $db->prepare("SELECT * FROM hastalar WHERE email =?");
		$query->execute([$email]);

		if ($query->fetch()) {
			die("Bu email ile daha önce giriş yapılmış!");
		}
	
	
	$password = $_POST['password'];
	$password_confirm = $_POST['password-confirm'];

	if ($password !== $password_confirm) {
		die("Şifreler aynı değil!");
	}

	$verification_code = rand(100000, 999999);
	$_SESSION['name'] = $_POST['name'];
	$_SESSION['surname'] = $_POST['surname'];
	$_SESSION['bdate'] = $_POST['bdate'];
	$_SESSION['gender'] = $_POST['gender'];
	$_SESSION['email'] = $email;
	$_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);
	$_SESSION['code'] = $verification_code;

	$_SESSION['code_time'] = time();

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
        $mail->addAddress($email);

        $mail->Subject = 'Doğrulama Kodu';
        $mail->Body = "Doğrulama kodunuz: " . $verification_code;

        $mail->send();

        header("Location: verify_email.php");
        exit;

    } catch (Exception $e) {
        echo "Mail gönderilemedi: {$mail->ErrorInfo}";
    }
}
?>
