<?php 
session_start();
require_once("db_baglanti.php");

// Oturum kontrolü (Giriş yapmamışsa login'e gönder)
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Güncel bilgileri veritabanından tekrar çekelim (Session eski kalmış olabilir)
$a_id = $_SESSION['admin_id'];
$sorgu = $db->prepare("SELECT * FROM yoneticiler WHERE a_id = ?");
$sorgu->execute([$a_id]);
$admin = $sorgu->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Bilgilerim | Yönetim Paneli</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style_panel_admin.css">
</head>
<body class="bg-light">

    <?php include("panel_menu_admin.php"); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                
                <?php if(isset($_SESSION['mesaj'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mesaj']['tur']; ?> alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa <?php echo $_SESSION['mesaj']['tur'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php 
                        echo $_SESSION['mesaj']['icerik']; 
                        unset($_SESSION['mesaj']); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div style="height: 100px; background-color: #36ACC2;"></div>
                    
                    <div class="card-body p-4 pt-0">
                        <div class="profile-avatar-container text-center" style="margin-top: -50px;">
                            <div class="profile-avatar shadow-sm border border-4 border-white mx-auto">
                                <?php echo strtoupper(substr($admin['a_ad'], 0, 1)); ?>
                            </div>
                            <h4 class="fw-bold mt-3 mb-1"><?php echo $admin['a_ad'] . " " . $admin['a_soyad']; ?></h4>
                            <span class="badge bg-light text-primary border px-3 py-2 rounded-pill">Sistem Yöneticisi</span>
                        </div>

                        <form action="db_islem.php" method="POST" class="mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Adınız</label>
                                    <input type="text" name="a_ad" class="form-control custom-input" value="<?php echo $admin['a_ad']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Soyadınız</label>
                                    <input type="text" name="a_soyad" class="form-control custom-input" value="<?php echo $admin['a_soyad']; ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">E-posta Adresiniz</label>
                                    <input type="email" name="email" class="form-control custom-input" value="<?php echo $admin['email']; ?>" required>
                                </div>

                                <div class="col-12 py-2">
                                    <hr class="opacity-25">
                                </div>

                                <div class="col-12">
                                    <h6 class="fw-bold"><i class="fa fa-shield-alt text-primary me-2"></i>Güvenlik Ayarları</h6>
                                    <p class="text-muted" style="font-size: 0.8rem;">Bilgilerinizi güncellemek veya şifrenizi değiştirmek için mevcut şifrenizi girmeniz zorunludur.</p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Mevcut Şifre</label>
                                    <input type="password" name="mevcut_sifre" class="form-control custom-input" placeholder="Onay için mevcut şifrenizi girin" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Yeni Şifre (İsteğe Bağlı)</label>
                                    <input type="password" name="yeni_sifre" class="form-control custom-input" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" name="admin_profil_guncelle" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm">
                                        <i class="fa fa-sync-alt me-2"></i>Bilgilerimi Güncelle
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Mesaj kutusunu 4 saniye sonra kapatma
        setTimeout(function() {
            let alert = document.querySelector(".alert");
            if(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    </script>
</body>
</html>