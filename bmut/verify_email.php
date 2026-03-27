<?php 
	session_start();

	if (!isset($_SESSION['code_time'])) {
		$remaining_time=0;
	}
	else
	{
		$remaining_time = 20 - (time()-$_SESSION['code_time']);
		if ($remaining_time < 0) {
			$remaining_time = 0;
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style_register.css">
	<script language="javascript" src="register_script.js"></script>
	<title></title>
</head>
<body>
	<div class="verify-div">
		<form action="save_patient.php" method="POST">
			<div class="verify-box">	
				<label for="email">Email: </label>
				<input type="email" name="email" id="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" readonly>
			</div>
			<div class="verify-box">
				<label for="code">Kodu Giriniz: </label>
				<input type="text" name="code" id="code" required>
			</div>
			<div>
				<button type="submit" id="btn_verify">Doğrula</button> 
			</div>
			<div class="verify-box">
				<p>Kalan Süre: <span id="timer"></span></p>
				
			</div>
			<div>
				<form action="resend_code.php" method="POST" >
					<button type="button" id="btn-send-again" style="display: none;" onclick="resendCode()">Kodu Tekrar Gönder</button>
				</form>
			</div>
			
		</form>
	</div>
</body>
</html>
<script>
	let timeLeft = <?php echo $remaining_time; ?>;

	function startTimer() {

    let timer = document.getElementById("timer");
    let btn_send_again = document.getElementById("btn-send-again");

    let interval = setInterval(function() {

        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        seconds = seconds < 10 ? "0" + seconds : seconds;

        timer.innerHTML = minutes + ":" + seconds;

        timeLeft--;

        if(timeLeft < 0){
            clearInterval(interval);
            timer.innerHTML = "Kodun süresi doldu!";
            btn_send_again.style.display = 'block'; 

        }
    }, 1000);
}
startTimer();

</script>