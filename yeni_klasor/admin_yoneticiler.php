<?php 
session_start();
require_once("db_baglanti.php");

// Tüm yöneticileri çek
$sorgu = $db->prepare("SELECT * FROM yoneticiler ORDER BY a_id ");
$sorgu->execute();
$yoneticiler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Yöneticiler</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style_panel.css">
</head>
<body class="bg-light">
    <?php include("panel_menu_admin.php"); ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fa fa-users-cog me-2 text-primary"></i>Sistem Yöneticileri</h2>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#adminEkleModal">
                <i class="fa fa-plus me-2"></i>Yeni Yönetici Ekle
            </button>
        </div>

		<?php if(isset($_SESSION['mesaj'])): ?>
		    <div class="alert alert-<?php echo $_SESSION['mesaj']['tur']; ?> alert-dismissible fade show" id="islem-mesaji">
		        <?php 
		            echo $_SESSION['mesaj']['icerik']; 
		            unset($_SESSION['mesaj']); 
		        ?>
		        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		    </div>
		<?php endif; ?>

        <div class="table-card">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Yönetici</th>
                        <th>E-posta</th>
                        <th class="text-center">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
	                    $sayac = 1;
	                    foreach($yoneticiler as $y): 
                    ?>
                    <tr>
                        <td class="text-muted fw-bold"><?php echo $sayac; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="admin-badge me-3"><?php echo strtoupper(substr($y['a_ad'], 0, 1)); ?></div>
                                <div>
                                    <div class="fw-bold"><?php echo $y['a_ad'] . " " . $y['a_soyad']; ?></div>
                                    <small class="text-muted">Sistem Yöneticisi</small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $y['email']; ?></td>
                        <td class="text-center">
                            <a href="db_islem.php?admin_sil=<?php echo $y['a_id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                    	$sayac++;
                		endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="adminEkleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="db_islem.php" method="POST" class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Yeni Yönetici Tanımla</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Ad</label>
                            <input type="text" name="a_ad" class="form-control" placeholder="Ad" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Soyad</label>
                            <input type="text" name="a_soyad" class="form-control" placeholder="Soyad" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@hastane.com" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" name="yeni_admin_ekle" class="btn btn-primary px-4">Yöneticiyi Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
	    // Sayfadaki mesajı bul
	    const mesaj = document.getElementById('islem-mesaji');
	    if (mesaj) {
	        // 3 saniye sonra (3000 ms) yavaşça kaybet
	        setTimeout(() => {
	            const alert = new bootstrap.Alert(mesaj);
	            alert.close();
	        }, 3000);
	    }
	</script>
</body>
</html>