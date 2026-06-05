<?php
session_start();
require_once("db_baglanti.php");
date_default_timezone_set('Europe/Istanbul');

// Eğer URL'de ay ve yıl varsa onları al, yoksa içinde bulunduğumuz ayı/yılı al
$ay = isset($_GET['ay']) ? (int)$_GET['ay'] : (int)date('m');
$yil = isset($_GET['yil']) ? (int)$_GET['yil'] : (int)date('Y');

$bugun = date('j');
$buAy = date('m');
$buYil = date('Y');

// İleri ve Geri butonları için hesaplama
$oncekiAy = $ay - 1;
$oncekiYil = $yil;
$sonrakiAy = $ay + 1;
$sonrakiYil = $yil;

if ($oncekiAy < 1) {
    $oncekiAy = 12;
    $oncekiYil--;
}
if ($sonrakiAy > 12) {
    $sonrakiAy = 1;
    $sonrakiYil++;
}

// Takvim hesaplamaları
$ilkGunIndeks = date('w', mktime(0, 0, 0, $ay, 1, $yil));
$boslukSayisi = ($ilkGunIndeks == 0) ? 6 : ($ilkGunIndeks - 1);
$aydakiGunSayisi = cal_days_in_month(CAL_GREGORIAN, $ay, $yil);

// Ay isimlerini Türkçeleştirelim
$aylar = [
    1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 
    'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
];
$gunler = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Randevu Al | Hasta Paneli</title>
	<link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style_randevu_al.css">
    <script src="js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
	<?php include('panel_menu_hasta.php'); ?>
	<?php if (isset($_GET['mesaj']) && $_GET['mesaj'] == 'basarili'): 
	    // Tarih formatını GG.AA.YYYY yapıyoruz
	    $gelenTarih = $_GET['tar'];
	    $yeniTarih = date("d.m.Y", strtotime($gelenTarih));
	?>
	    <div class="alert alert-success shadow-lg border-0 p-4 mt-3 mb-4 rounded-3" role="alert">
	        <div class="d-flex align-items-center mb-3">
	            <h4 class="alert-heading mb-0">Randevunuz Onaylandı!</h4>
	        </div>
	        
	        <p class="mb-3">
	            Sayın <strong><?php echo $_SESSION['ad'] . " " . $_SESSION['soyad']; ?></strong>, randevunuz başarıyla kaydedilmiştir.
	        </p>
	        
	        <div class="bg-white bg-opacity-50 p-3 rounded-2 mb-3 text-dark">
	            <div class="row g-3">
	                <div class="col-6 col-md-3">
	                    <small class="text-muted d-block font-weight-bold">Poliklinik</small>
	                    <strong><?php echo htmlspecialchars($_GET['pol']); ?></strong>
	                </div>
	                <div class="col-6 col-md-3">
	                    <small class="text-muted d-block">Doktor</small>
	                    <strong><?php echo htmlspecialchars($_GET['dok']); ?></strong>
	                </div>
	                <div class="col-6 col-md-3">
	                    <small class="text-muted d-block">Tarih</small>
	                    <strong><?php echo $yeniTarih; ?></strong>
	                </div>
	                <div class="col-6 col-md-3">
	                    <small class="text-muted d-block">Saat</small>
	                    <strong><?php echo htmlspecialchars($_GET['saat']); ?></strong>
	                </div>
	            </div>
	        </div>

	        <div class="d-flex justify-content-end">
	            <button type="button" class="btn btn-success px-5 fw-bold shadow-sm" data-bs-dismiss="alert">
	                Tamam
	            </button>
	        </div>
	    </div>
	<?php endif; ?>

	<div class="card p-4 shadow-sm">
	    <form id="randevu_al" action="db_islem.php" method="POST">
	        
	        <div class="mb-3">
	            <label class="form-label fw-bold">Poliklinik Seçin</label>
	            <select id="poliklinik" name="poliklinik_id" class="form-select">
	                <option value="">Seçiniz...</option>
	                <?php
	                $sorgu = $db->query("SELECT * FROM poliklinikler");
	                while($row = $sorgu->fetch()){
	                    echo "<option value='".$row['p_id']."'>".$row['p_ad']."</option>";
	                }
	                ?>
	            </select>
	        </div>

	        <div id="doktor_alani" class="mb-3 d-none">
	            <label class="form-label fw-bold">Doktor Seçin</label>
	            <select id="doktor" name="doktor_id" class="form-select"></select>
	        </div>

	        <div id="takvim_dis_alan" class="d-none">
	            <div class="takvim-header d-flex justify-content-around align-items-center mb-3">
	                <a href="?ay=<?php echo $oncekiAy; ?>&yil=<?php echo $oncekiYil; ?>" class="btn btn-sm btn-outline-secondary">&lt; Önceki Ay</a>
	                <h4 class="mb-0"><?php echo $aylar[$ay] . " " . $yil; ?></h4>
	                <a href="?ay=<?php echo $sonrakiAy; ?>&yil=<?php echo $sonrakiYil; ?>" class="btn btn-sm btn-outline-secondary">Sonraki Ay &gt;</a>
	            </div>

	            <div class="takvim-container">
	                <table class="takvim-tablo">
	                    <thead>
	                        <tr>
	                            <?php foreach($gunler as $g) echo "<th class='text-center small text-muted'>$g</th>"; ?>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr>
	                            <?php
	                            for ($i = 0; $i < $boslukSayisi; $i++) echo "<td></td>";

	                            for ($gun = 1; $gun <= $aydakiGunSayisi; $gun++) {
	                                if (($gun + $boslukSayisi - 1) % 7 == 0 && $gun != 1) echo "</tr><tr>";

	                                $zamanDamgasi = mktime(0, 0, 0, $ay, $gun, $yil);
	                                $gercekGun = date('w', $zamanDamgasi);
	                                $haftaSonuMu = ($gercekGun == 0 || $gercekGun == 6);
	                                
	                                if ($yil == $buYil && $ay == $buAy) {
	                                    $gecmisMi = ($gun < $bugun);
	                                } elseif ($yil < $buYil || ($yil == $buYil && $ay < $buAy)) {
	                                    $gecmisMi = true;
	                                } else {
	                                    $gecmisMi = false;
	                                }

	                                $disabled = ($haftaSonuMu || $gecmisMi) ? "disabled" : "";
	                                $ekstraClass = ($gun == $bugun) ? "bugun-stil" : "";
	                                if ($haftaSonuMu) $ekstraClass .= " text-danger opacity-50";

	                                echo "<td>
	                                        <button type='button' class='gun-btn $ekstraClass' $disabled data-gun='$gun'>
	                                            $gun
	                                        </button>
	                                      </td>";
	                            }
	                            ?>
	                        </tr>
	                    </tbody>
	                </table>
	            </div>
	        </div>

	        <div id="saat_alani" class="mb-3 d-none">
	            <label class="form-label fw-bold">Saat Seçin</label>
	            <div id="saat_listesi" class="d-flex flex-wrap gap-2"></div>
	            <input type="hidden" name="secilen_saat" id="secilen_saat">
	        </div>

	        <input type="hidden" name="poliklinik_ad" id="poliklinik_ad">
    		<input type="hidden" name="doktor_ad" id="doktor_ad">
  		    <input type="hidden" name="secilen_tarih" id="final_tarih">
	        
	        <button type="submit" id="tamamla_btn" name="randevu_al" class="btn btn-primary w-100 mt-3 d-none">Randevuyu Tamamla</button>
	    </form>
	</div>
	<script>
	    // PHP'deki ay ve yıl bilgisini JS'in anlayacağı bir yere bırakıyoruz
	    var aktifAy = "<?php echo $ay; ?>";
	    var aktifYil = "<?php echo $yil; ?>";
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="hasta_randevu_al.js"></script>
</body>
</html>