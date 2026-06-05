<?php 
session_start();
require_once("db_baglanti.php");

$doctor_id = $_SESSION['doctor_id'];
$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'bugun';
$bugun = date('Y-m-d');
$su_an = date('H:i:s'); // EKLEME: Kontrol için şu anki saati aldık
$tarih_bas = $_GET['tarih_bas'] ?? null;
$tarih_bitis = $_GET['tarih_bitis'] ?? null;

// Bu kısmı mevcut $randevular sorgusunun hemen altına ekle
$onay_bekleyen_sayisi = 0;

// Filtrelerden bağımsız, doktorun bekleyen tüm randevularını sayan sorgu
$sayac_sorgu = $db->prepare("SELECT randevu_tarihi, randevu_saati, randevu_durum FROM randevular WHERE doktor_id = ? AND 
                            (randevu_durum = 'Bekliyor' OR randevu_durum IS NULL OR randevu_durum = '')");
$sayac_sorgu->execute([$doctor_id]);
$tum_bekleyenler = $sayac_sorgu->fetchAll(PDO::FETCH_ASSOC);

foreach ($tum_bekleyenler as $onay_r) {
    $r_tarih_saat = strtotime($onay_r['randevu_tarihi'] . ' ' . $onay_r['randevu_saati']);
    // Eğer randevu zamanı şu andan (time()) küçükse veya eşitse
    if ($r_tarih_saat <= time()) {
        $onay_bekleyen_sayisi++;
    }
}

// 1. Temel SQL Sorgusu
$sql = "SELECT r.*, h.h_ad, h.h_soyad 
        FROM randevular r 
        JOIN hastalar h ON r.hasta_id = h.h_id 
        WHERE r.doktor_id = ?";

$params = [$doctor_id];

// 2. Filtreleme Mantığı
if ($tarih_bas && $tarih_bitis) {
    if ($tarih_bas < $tarih_bitis) {
        $sql .= " AND r.randevu_tarihi BETWEEN ? AND ? ORDER BY r.randevu_saati ASC";
        $params[] = $tarih_bas;
        $params[] = $tarih_bitis;    
    }
    else
    {
        $hata_mesaji = "Başlangıç tarihi, bitiş tarihinden sonra olamaz. Lütfen tarih aralığını kontrol ediniz.";
    }
    
} 
else {
    if ($filtre == 'bugun') {
        $sql .= " AND r.randevu_tarihi = ? ORDER BY r.randevu_saati ASC";
        $params[] = $bugun;
    } 
    elseif ($filtre == 'gelecek') {
        $sql .= " AND r.randevu_tarihi > ? ORDER BY r.randevu_tarihi ASC, r.randevu_saati ASC";
        $params[] = $bugun;
    } 
    elseif ($filtre == 'gecmis') {
        $sql .= " AND r.randevu_tarihi < ? ORDER BY r.randevu_tarihi DESC, r.randevu_saati DESC";
        $params[] = $bugun;
    }
}

$sorgu = $db->prepare($sql);
$sorgu->execute($params);
$randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Randevularım | Doktor Paneli</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <?php include('panel_menu_doktor.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-4">
                    <h2 class="fw-bold text-dark">
                        <i class="fa fa-calendar-check me-2 text-primary mb-4 mt-4"></i>Randevu Takvimi
                    </h2>
                </div>
                <?php if ($onay_bekleyen_sayisi > 0): ?>
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                        <i class="fa fa-exclamation-triangle me-3 fa-2x text-warning"></i>
                        <div>
                            <h5 class="alert-heading mb-1 fw-bold">Onay Bekleyen Randevular Var!</h5>
                            <span>Şu ana kadar tamamlanmış ancak durumu (Geldi/Gelmedi) işaretlenmemiş <strong><?php echo $onay_bekleyen_sayisi; ?></strong> randevu bulunuyor. Lütfen listeden güncelleyiniz.</span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($hata_mesaji)): ?>
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="fa fa-exclamation-circle me-3 fa-lg"></i>
                        <div>
                            <strong>Hatalı Tarih Aralığı:</strong> <?php echo $hata_mesaji; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4" >
                    <form action="" method="GET" class="d-flex align-items-end gap-2" style="max-width: 400px; ">
                        <div style="flex: 1">
                            <label for="tarih_bas" class="form-label small fw-bold text-muted mb-1">Başlangıç Tarihi</label>
                            <input type="date" name="tarih_bas" id="tarih_bas" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['tarih_bas'] ?? ''); ?>" style="height: 40px;">
                        </div>
                        <div style="flex: 1">
                            <label for="tarih_bitis" class="form-label small fw-bold text-muted mb-1">Bitiş Tarihi</label>
                            <input type="date" name="tarih_bitis" id="tarih_bitis" class="form-control" 
                                   value="<?php echo htmlspecialchars($_GET['tarih_bitis'] ?? ''); ?>" style="height: 40px;">
                        </div>
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary" style="height:40px; width: 100px;">
                                <i class="fa fa-search"></i> Filtrele
                            </button>
                            <?php if(isset($_GET['tarih_bas']) || isset($_GET['tarih_bitis'])): ?>
                                <a href="doktor_randevular.php" class="btn btn-outline-danger" title="Filtreyi Temizle">
                                    <i class="fa fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="btn-group shadow-sm" style="height: 40px;" >
                        <a href="doktor_randevular.php?filtre=gecmis" 
                           class="btn btn-white border <?php echo ($filtre == 'gecmis') ? 'active btn-secondary text-white' : ''; ?>">
                           <i class="fa fa-history me-1"></i> Geçmiş
                        </a>
                        <a href="doktor_randevular.php?filtre=bugun" 
                           class="btn btn-white border <?php echo ($filtre == 'bugun') ? 'active btn-primary text-white' : ''; ?>">
                           Bugün
                        </a>
                        <a href="doktor_randevular.php?filtre=gelecek" 
                           class="btn btn-white border <?php echo ($filtre == 'gelecek') ? 'active btn-success text-white' : ''; ?>">
                           Gelecek <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Hasta Bilgileri</th>
                                        <th class="text-center">Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($randevular) > 0): ?>
                                        <?php $sayac = 1; foreach($randevular as $r): 
                                            // EKLEME: Zaman Kontrol Mantığı
                                            $r_tarih = $r['randevu_tarihi'];
                                            $r_saat = $r['randevu_saati'];
                                            
                                            // Randevu bugün mü, geçmişte mi yoksa gelecekte mi?
                                            $saat_geldi_mi = false;
                                            if ($r_tarih < $bugun) {
                                                $saat_geldi_mi = true; // Geçmiş günler zaten tamam
                                            } elseif ($r_tarih == $bugun) {
                                                if ($r_saat <= $su_an) {
                                                    $saat_geldi_mi = true; // Bugün ama saati geçmiş veya şu an
                                                }
                                            }
                                            // Gelecek tarihler için zaten false kalacak
                                        ?>
                                        <tr>
                                            <td class="ps-4 text-muted fw-bold"><?php echo $sayac++; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($r['randevu_tarihi'])); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?php echo $r['randevu_saati']; ?></span></td>
                                            <td>
                                                <div class="fw-bold"><?php echo $r['h_ad'] . " " . $r['h_soyad']; ?></div>
                                            </td>
                                            <td class="text-center" style="width: 200px;">
                                                <?php if ($r['randevu_durum'] == 'İptal Edildi'): ?>
                                                    <span class="badge bg-danger-subtle text-danger p-2 w-100">
                                                        <i class="fa fa-ban me-1"></i> İptal Edildi
                                                    </span>
                                                <?php else: ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle w-100 <?php 
                                                            if(!$saat_geldi_mi) echo 'btn-light text-muted'; // Saat gelmediyse soluk renk
                                                            else echo ($r['randevu_durum'] == 'Geldi') ? 'btn-success' : (($r['randevu_durum'] == 'Gelmedi') ? 'btn-warning' : 'btn-outline-primary'); 
                                                        ?>" type="button" data-bs-toggle="dropdown" <?php echo !$saat_geldi_mi ? 'disabled' : ''; ?>>
                                                            <?php 
                                                                if(!$saat_geldi_mi) echo '<i class="fa fa-clock me-1"></i> Bekleniyor';
                                                                else echo $r['randevu_durum'] ?: 'Seçiniz'; 
                                                            ?>
                                                        </button>
                                                        
                                                        <?php if($saat_geldi_mi): // Sadece saat geldiyse menü oluşsun ?>
                                                        <ul class="dropdown-menu shadow border-0">
                                                            <li>
                                                                <a class="dropdown-item text-success fw-bold" href="db_islem.php?randevu_id=<?php echo $r['randevu_id']; ?>&yeni_durum=Geldi">
                                                                    <i class="fa fa-check-circle me-2"></i> Geldi
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger fw-bold" href="db_islem.php?randevu_id=<?php echo $r['randevu_id']; ?>&yeni_durum=Gelmedi">
                                                                    <i class="fa fa-times-circle me-2"></i> Gelmedi
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fa fa-calendar-times fa-3x mb-3 d-block"></i>
                                                Seçili filtreye uygun randevu bulunamadı.
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
</body>
</html>