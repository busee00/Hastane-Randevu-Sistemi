<?php 
session_start();
require_once("db_baglanti.php");
date_default_timezone_set('Europe/Istanbul'); // Zaman kontrolü için Türkiye saati ayarı

$hasta_id = $_SESSION['user_id'];

// Hastanın randevularını doktor ve poliklinik bilgileriyle beraber çekiyoruz
// Tablodaki randevu_id sütununun adının senin veritabanında 'randevu_id' olduğundan emin ol (bazı yerlerde r_id olabilir)
$sql = "SELECT r.*, d.unvan, d.d_ad, d.d_soyad, p.p_ad 
        FROM randevular r 
        JOIN doktorlar d ON r.doktor_id = d.d_id 
        JOIN poliklinikler p ON d.poliklinik_id = p.p_id 
        WHERE r.hasta_id = ? 
        ORDER BY r.randevu_tarihi DESC, r.randevu_saati DESC";

$sorgu = $db->prepare($sql);
$sorgu->execute([$hasta_id]);
$randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Randevularım | Hasta Paneli </title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        .temp-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 300px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <?php include('panel_menu_hasta.php'); ?>
    <?php if(isset($_GET['durum']) && $_GET['durum'] == 'ok' && isset($_GET['islem']) && $_GET['islem'] == 'degerlendirme'): ?>
        <div class="container mt-3">
            <div class="alert shadow-sm border-0 animate__animated animate__fadeIn" style="background-color: #eef9fb; border-left: 5px solid #36ACC2 !important;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-check fa-2x" style="color: #36ACC2;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold" style="color: #2c7a8a;">Değerlendirmeniz Alındı!</h6>
                        <p class="mb-0 small text-muted">Geri bildiriminiz için teşekkür ederiz. Görüşleriniz hizmet kalitemizi artırmak için bizim için çok değerli.</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['durum']) && $_GET['durum'] == 'no'): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Hata!</strong> Değerlendirme kaydedilirken bir sorun oluştu. Lütfen daha sonra tekrar deneyin.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-list-check me-2 text-success"></i>Randevu Geçmişim
                    </h2>
                    <a href="hasta_randevu_al.php" class="btn btn-success shadow-sm">
                        <i class="fa fa-plus me-1"></i> Yeni Randevu Al
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Tarih / Saat</th>
                                        <th>Poliklinik</th>
                                        <th>Doktor</th>
                                        <th>Durum</th>
                                        <th class="text-center">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($randevular) > 0): ?>
                                        <?php foreach($randevular as $r): 
                                            // Randevu zamanını ve şu anki zamanı hesapla
                                            $randevu_zamani_str = $r['randevu_tarihi'] . ' ' . $r['randevu_saati'];
                                            $randevu_timestamp = strtotime($randevu_zamani_str);
                                            $su_an = time();
                                            $fark_saat = ($su_an - $randevu_timestamp) / 3600;
                                            
                                            $durum = $r['randevu_durum'] ?: 'Bekliyor';
                                            
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold"><?php echo date('d.m.Y', strtotime($r['randevu_tarihi'])); ?></div>
                                                <small class="text-muted"><?php echo $r['randevu_saati']; ?></small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border"><?php echo $r['p_ad']; ?></span></td>
                                            <td> <?php echo $r['unvan'] . " " . $r['d_ad'] . " " . $r['d_soyad']; ?></td>
                                            <td>
                                                <?php 
                                                $renk = 'bg-primary';
                                                if($durum == 'Geldi') $renk = 'bg-success';
                                                else if($durum == 'Gelmedi') $renk = 'bg-warning text-dark';
                                                else if($durum == 'İptal Edildi') $renk = 'bg-danger';
                                                ?>
                                                <span class="badge <?php echo $renk; ?>"><?php echo $durum; ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if($durum == 'Bekliyor'): ?>
                                                    <a href="db_islem.php?iptal_id=<?php echo $r['randevu_id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Randevuyu iptal etmek istediğinize emin misiniz?')">
                                                        İptal Et
                                                    </a>

                                                <?php elseif($durum == 'Geldi'): ?>
                                                    <?php if($fark_saat <= 48): ?>
                                                        <a href="hasta_anket.php?id=<?php echo $r['randevu_id']; ?>" 
                                                           class="btn btn-sm btn-success">
                                                            <i class="fas fa-star me-1"></i>Değerlendir
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-secondary degeri-gecmis">
                                                            Değerledir
                                                        </button>
                                                    <?php endif; ?>

                                                <?php else: ?>
                                                    <small class="text-muted"></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fa fa-calendar-xmark fa-3x mb-3 d-block"></i>
                                                Henüz bir randevu kaydınız bulunmuyor.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // "Süresi Geçti" butonuna tıklandığında uyarı ver
        $(document).on('click', '.degeri-gecmis', function() {
            // Varsa eski uyarıyı sil
            $('.temp-alert').remove();

            // Yeni mesajı oluştur
            var alertHtml = '<div class="temp-alert alert alert-danger shadow-lg border-0">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>' +
                            'Değerlendirme süresi (48 saat) geçmiştir.' +
                            '</div>';

            $('body').append(alertHtml);

            // 5 saniye sonra yavaşça kaybol ve sil
            setTimeout(function() {
                $('.temp-alert').fadeOut(1000, function() {
                    $(this).remove();
                });
            }, 5000);
        });
    });
    </script>
    <script>
        // Sayfa yüklendiğinde çalıştır
        window.addEventListener('DOMContentLoaded', (event) => {
            // Tüm alert mesajlarını bul
            const alerts = document.querySelectorAll('.alert');
            
            alerts.forEach(function(alert) {
                // 5000 milisaniye (5 saniye) sonra
                setTimeout(() => {
                    // Bootstrap'in kendi fade özelliğini kullanarak kapat
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>