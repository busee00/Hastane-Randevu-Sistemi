<?php 
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Yönetici Paneli</title>
	<link rel="stylesheet" type="text/css" href="style_panel.css">
	<link href="css/bootstrap.css" rel="stylesheet">
	<script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
	<div class="navbar-admin">
		<?php 
		if (isset($_SESSION['ad']) && isset($_SESSION['soyad'])) {
			$ad = $_SESSION['ad'];
			$soyad = $_SESSION['soyad'];

			echo "<h2> Hoşgeldiniz, " . $ad . " " . $soyad . "</h2>" ;
		}
		?>
		
	</div>

	<nav class="navbar bg-body-tertiary fixed-top">
  		<div class="container-fluid">
		    	<?php 
				session_start();
				if (isset($_SESSION['ad']) && isset($_SESSION['soyad'])) {
					$ad = $_SESSION['ad'];
					$soyad = $_SESSION['soyad'];

					echo "<h2> Hoşgeldiniz, " . $ad . " " . $soyad . "</h2>" ;
				}
				?>
		    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
		      <span class="navbar-toggler-icon"></span>
    		</button>

	    	<div class="offcanvas offcanvas-end panel-menu" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">

	        	<div class="offcanvas-header menu-ust">
	        		<button type="button" class="btn-close btn-kapat" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	        		<div class="text-center profil-alani">
	        			<img class="rounded-circle profil-foto" src="images/admin.jpeg">	        			
		        		<?php 
						if (isset($_SESSION['ad']) && isset($_SESSION['soyad'])) {
							$ad = $_SESSION['ad'];
							$soyad = $_SESSION['soyad'];

							echo "<h5>" . $ad . " " . $soyad . "</h5>" ;
						}
						?>
	        		</div>
	     		</div>

	        	<div class="offcanvas-body">
			        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
			          <li class="nav-item">
			            <a class="nav-link active" aria-current="page" href="panel_admin.php">Anasayfa</a>
			          </li>
			          <li class="nav-item">
			            <a class="nav-link" href="#">Bilgilerim</a>
			          </li>
			          <li class="nav-item">
			            <a class="nav-link" href="#">Yöneticiler</a>
			          </li>
			          <li class="nav-item">
			            <a class="nav-link" href="admin_pol_list.php">Poliklinikler ve Doktorlar</a>
			          </li>
			          <li class="nav-item">
			            <a class="nav-link" href="#">Randevular</a>
			          </li>
			          <li class="nav-item">
			            <a class="nav-link" href="#">Memnuniyet Grafikleri</a>
			          </li>
			           <li class="nav-item">
			            <a class="nav-link" href="anasayfa.php">Çıkış Yap</a>
			          </li>
			        </ul>
	      		</div>

	    	</div>
  		</div>
	</nav>

</body>
</html>