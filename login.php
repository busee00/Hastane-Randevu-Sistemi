<?php session_start(); ?>
<?php if (isset($_SESSION['hata'])): ?>
		<p style="color: red;"><?= $_SESSION['hata'] ?></p>
		<?php unset($_SESSION['hata']); ?>
<?php endif; ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style_login.css">
	<script language="javascript" src="script_login.js"></script>
	<title>Giriş Yap</title>
</head>
<body>
	<h1>Giriş Yap</h1>
	<h5>Giriş yapmak için seçim yapınız.</h5>
	<div class="login">
		<div class="log-form" id="form">
			<form method="POST" action="login_control.php">
				<div class="role-box">
				    <label class="role">
				        <input type="radio" name="role" value="patient" hidden>
				        <img src="images/patient.jpeg" alt="patient">
				        <span>Hasta</span>
				    </label>
				    <label class="role">
				        <input type="radio" name="role" value="doctor" hidden>
				        <img src="images/doctor.jpeg" alt="doctor">
				        <span>Doktor</span>
				    </label>
				    <label class="role">
				        <input type="radio" name="role" value="admin" hidden>
				        <img src="images/admin.jpeg" alt="admin">
				        <span>Yönetici</span>
				    </label>
				</div>
				<div class="input-box" style="margin-top:30px;;"><input type="email" name="email" placeholder="Email"></div>
				<div class="input-box"><input type="password" name="password" placeholder="Şifre"></div>
				<div class="input-box"><button type="submit" name="btn-login">Giriş Yap</button></div>
			</form>
		</div>
	</div>
</body>
</html>