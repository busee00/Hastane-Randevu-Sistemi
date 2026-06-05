<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kayıt Ol</title>
	<link rel="stylesheet" type="text/css" href="style_register.css">
	<script language="javascript" src="register_script.js"></script>
</head>
<body class="register">
	<div class="reg_form">
		<h1>Kaydol</h1>
		<form action="register.php" method="POST" class="form" onsubmit="return checkPasswords()">
			<div class="input-box">
				<label for="name">Ad </label>
				<input type="text" name="name" id="name" required> <!-- Required boş göndermeyi engeller-->
			</div>

			<div class="input-box">
				<label for="surname">Soyad </label>
				<input type="text" name="surname" id="surname" required>
			</div>

			<div class="input-box">
				<label for="bdate">Doğum Tarihi </label>
				<input type="date" name="bdate" id="bdate" required>
			</div>

			<div class="input-box">
				<label for="gender">Cinsiyet: </label>

				<input type="radio" name="gender" id="female" value="Kadın">
				<label for="female">Kadın</label>

				<input type="radio" name="gender" id="male" value="Erkek">
				<label for="male">Erkek</label>
			</div>

			<div class="input-box">
				<label for="email">E-mail: </label>
				<input type="email" name="email" id="email" placeholder="ornek@ornek.com" required>
			</div>

			<div class="input-box">
				<label for="password">Şifre: </label>
				<div class="password-box">
					<input type="password" name="password" id="password" minlength="8" aria-describedby="passwordHelpBlock" required>
					<span class="toggle-password" onclick="togglePassword()">
						<img id="icon_password" src="images/visibility_password.svg"
						alt="Şifreyi Göster">
					</span>
					<div id="passwordHelpBlock" class="form-text" style="font-size:13px;"> 
						Şifreniz en az 8 karakterden oluşmalıdır.
					</div>
				</div>
			</div>

			<div class="input-box"><!--Yazım hatasını önlemek için şifre tekrar isteniyor-->
				<label for="password-confirm">Şifre Tekrar: </label>
				<div class="password-box">
					<input type="password" name="password-confirm" id="password-confirm" minlength="8" required>
					<span class="toggle-password" onclick="togglePasswordConfirm()">
						<img id="icon_password_confirm" src="images/visibility_password.svg" alt="Şifreyi Göster">
					</span>
				</div>
				
			</div>	

			<div class="buttons">
				<button class="btn-reg" type="submit">Kaydol</button>
				<button class="btn-reg" type="reset">Temizle</button>
				
			</div>
			<div class="login-link" style="font-size: 13px;">
				<p>Hesabın var mı?  <a href="login.php"> Giriş Yap </a></p>
			</div>
			
		</form>
	</div>
</body>
</html>