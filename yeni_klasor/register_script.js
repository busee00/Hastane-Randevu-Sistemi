function togglePassword() {
	let passwordInput = document.getElementById('password');
	let passwordIcon = document.getElementById('icon_password');

	if (passwordInput.type == 'password') 
	{
		passwordInput.type = 'text';
		passwordIcon.src = "images/visibility_off_password.svg";

	}
	else
	{
		passwordInput.type = 'password';
		passwordIcon.src = "images/visibility_password.svg";
	}
}

function togglePasswordConfirm() {
	let passwordInput2 = document.getElementById('password-confirm');
	let passwordIcon2 = document.getElementById('icon_password_confirm');

	if (passwordInput2.type === 'password') 
	{
		passwordInput2.type = 'text';
		passwordIcon2.src = "images/visibility_off_password.svg";

	}
	else
	{
		passwordInput2.type = 'password';
		passwordIcon2.src = "images/visibility_password.svg";
	}
}

function checkPassword(){
	let password = document.getElementById('password').value;
	let passwordConfirm = document.getElementById('passwordConfirm').value;

	if (password !== passwordConfirm) 
	{
		alert('Şifreler aynı değil!');
		return false;
	} 
	return true;
}
function resendCode(){
    window.location.href = "resend_code.php";
}