<link rel="stylesheet" type="text/css" href="style_panel.css">
<nav class="navbar bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="admin_randevular.php">
            <i class="fa-solid fa-hospital-user me-2"></i> Yönetim Paneli
        </a>

        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['ad'])): ?>
                <span class="me-3 d-none d-md-inline text-muted">
                    Hoş geldiniz, <strong><?php echo $_SESSION['ad'] . " " . $_SESSION['soyad']; ?></strong>
                </span>
            <?php endif; ?>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" style="width: 300px;">
            <div class="offcanvas-header menu-ust border-bottom">
			    <button type="button" class="btn-close btn-kapat" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			    <div class="profil-alani">
			        <img class="profil-foto" src="images/admin.jpeg" alt="Admin">
			        <?php if (isset($_SESSION['ad'])): ?>
			            <h6 class="mt-2 mb-0"><?php echo $_SESSION['ad'] . " " . $_SESSION['soyad']; ?></h6>
			            <small class="text-muted">Yönetici</small>
			        <?php endif; ?>
			    </div>
			</div>

            <div class="offcanvas-body p-0">
                <?php 
				// Mevcut sayfanın dosya adını alıyoruz
				$aktif_sayfa = basename($_SERVER['PHP_SELF']); 
				?>

				<ul class="navbar-nav">
				    <li class="nav-item border-bottom">
				        <a class="nav-link px-4 py-3 <?php echo ($aktif_sayfa == 'admin_randevular.php') ? 'fw-bold text-primary' : ''; ?>" href="admin_randevular.php">
				            <i class="fa-solid fa-calendar-check me-3 text-primary"></i> Randevular
				        </a>
				    </li>
				    <li class="nav-item border-bottom">
				        <a class="nav-link px-4 py-3 <?php echo ($aktif_sayfa == 'admin_pol_list.php') ? 'fw-bold text-primary' : ''; ?>" href="admin_pol_list.php">
				            <i class="fa-solid fa-stethoscope me-3 text-primary"></i> Poliklinikler & Doktorlar
				        </a>
				    </li>
				    <li class="nav-item border-bottom">
				        <a class="nav-link px-4 py-3 <?php echo ($aktif_sayfa == 'admin_grafikler.php') ? 'fw-bold text-primary' : ''; ?>" href="admin_grafikler.php">
				            <i class="fa-solid fa-chart-line me-3 text-primary"></i> Memnuniyet Grafikleri
				        </a>
				    </li>
				    <li class="nav-item border-bottom">
				        <a class="nav-link px-4 py-3 <?php echo ($aktif_sayfa == 'admin_yoneticiler.php') ? 'fw-bold text-primary' : ''; ?>" href="admin_yoneticiler.php">
				            <i class="fa-solid fa-user-shield me-3 text-primary"></i> Yöneticiler
				        </a>
				    </li>
				    <li class="nav-item border-bottom">
				        <a class="nav-link px-4 py-3 <?php echo ($aktif_sayfa == 'admin_profil.php') ? 'fw-bold text-primary' : ''; ?>" href="admin_profil.php">
				            <i class="fa-solid fa-gear me-3 text-primary"></i> Bilgilerim
				        </a>
				    </li>
				    <li class="nav-item">
				        <a class="nav-link px-4 py-3 text-danger <?php echo ($aktif_sayfa == 'cikis.php') ? 'fw-bold' : ''; ?>" href="cikis.php">
				            <i class="fa-solid fa-right-from-bracket me-3"></i> Çıkış Yap
				        </a>
				    </li>
				</ul>
            </div>
        </div>
    </div>
</nav>

<div style="margin-bottom: 90px;"></div>