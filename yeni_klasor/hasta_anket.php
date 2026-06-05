<?php 
session_start();
require_once("db_baglanti.php");

// Randevu ID kontrolü
if(!isset($_GET['id'])) {
    header("Location: randevularim.php");
    exit();
}

$randevu_id = $_GET['id'];

// Randevu bilgilerini çekelim (Hangi doktoru değerlendiriyor görelim)
$sorgu = $db->prepare("SELECT r.*, d.d_ad, d.d_soyad, p.p_ad 
                       FROM randevular r 
                       JOIN doktorlar d ON r.doktor_id = d.d_id 
                       JOIN poliklinikler p ON d.poliklinik_id = p.p_id 
                       WHERE r.randevu_id = ?");
$sorgu->execute([$randevu_id]);
$randevu = $sorgu->fetch(PDO::FETCH_ASSOC);

if(!$randevu) {
    header("Location: hasta_randevular.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Değerlendirme Anketi | Hasta Paneli</title>
	<link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style_randevu_degerlendir.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <?php include("panel_menu_hasta.php"); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card card-degerlendirme shadow-sm">
                    <div class="card-header card-header-turkuaz text-white">
                        <h4 class="mb-0 text-center fw-bold">
                            <i class="fa-solid fa-star-half-stroke me-2"></i>Randevu Değerlendirme Formu
                        </h4>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="info-box-panel p-3 mb-4">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-circle-info me-2"></i>Randevu Bilgileri</h6>
                            <div class="small text-muted">
                                <strong>Poliklinik:</strong> <?php echo $randevu['p_ad']; ?><br>
                                <strong>Doktor:</strong> <?php echo $randevu['unvan'] . " " . $randevu['d_ad'] . " " . $randevu['d_soyad']; ?><br>
                                <strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($randevu['randevu_tarihi'])); ?>
                            </div>
                        </div>

                        <form action="db_islem.php" method="POST">
                            <input type="hidden" name="randevu_id" value="<?php echo $randevu_id; ?>">
                            <input type="hidden" name="doktor_id" value="<?php echo $randevu['doktor_id']; ?>">

                            <div class="info-box-panel p-3 mb-4 text-center">
						        <h6 class="fw-bold text-primary mb-1">Hizmet Değerlendirmesi</h6>
						        <p class="small text-muted mb-0">Lütfen aşağıdaki kriterleri 1-5 arası puanlayın.</p>
						    </div>

						    <div class="anket-soru-row mb-3">
						        <label class="soru-metni d-block mb-2">1. Doktorun yaklaşımını (nezaket, ilgi, saygı) nasıl değerlendirirsiniz?</label>
						        <div class="star-rating">
						            <input type="radio" id="q1-5" name="soru1" value="5" required /><label for="q1-5" class="fas fa-star"></label>
						            <input type="radio" id="q1-4" name="soru1" value="4" /><label for="q1-4" class="fas fa-star"></label>
						            <input type="radio" id="q1-3" name="soru1" value="3" /><label for="q1-3" class="fas fa-star"></label>
						            <input type="radio" id="q1-2" name="soru1" value="2" /><label for="q1-2" class="fas fa-star"></label>
						            <input type="radio" id="q1-1" name="soru1" value="1" /><label for="q1-1" class="fas fa-star"></label>
						        </div>
						    </div>

                            <div class="anket-soru-row mb-3">
						        <label class="soru-metni d-block mb-2">2. Doktorunuzun sizi dinleme düzeyini nasıl değerlendirirsiniz?</label>
						        <div class="star-rating">
						            <input type="radio" id="q2-5" name="soru2" value="5" required /><label for="q2-5" class="fas fa-star"></label>
						            <input type="radio" id="q2-4" name="soru2" value="4" /><label for="q2-4" class="fas fa-star"></label>
						            <input type="radio" id="q2-3" name="soru2" value="3" /><label for="q2-3" class="fas fa-star"></label>
						            <input type="radio" id="q2-2" name="soru2" value="2" /><label for="q2-2" class="fas fa-star"></label>
						            <input type="radio" id="q2-1" name="soru2" value="1" /><label for="q2-1" class="fas fa-star"></label>
						        </div>
						    </div>

						    <div class="anket-soru-row mb-4">
						        <label class="soru-metni d-block mb-2">3. Doktorun size ayırdığı süre yeterli miydi?</label>
						        <div class="star-rating">
						            <input type="radio" id="q3-5" name="soru3" value="5" required /><label for="q3-5" class="fas fa-star"></label>
						            <input type="radio" id="q3-4" name="soru3" value="4" /><label for="q3-4" class="fas fa-star"></label>
						            <input type="radio" id="q3-3" name="soru3" value="3" /><label for="q3-3" class="fas fa-star"></label>
						            <input type="radio" id="q3-2" name="soru3" value="2" /><label for="q3-2" class="fas fa-star"></label>
						            <input type="radio" id="q3-1" name="soru3" value="1" /><label for="q3-1" class="fas fa-star"></label>
						        </div>
						    </div>

						    <div class="anket-soru-row mb-4">
						        <label class="soru-metni d-block mb-2">4. Doktorun açıklamalarını ne kadar anlaşılır buldunuz?</label>
						        <div class="star-rating">
						            <input type="radio" id="q4-5" name="soru4" value="5" required /><label for="q4-5" class="fas fa-star"></label>
						            <input type="radio" id="q4-4" name="soru4" value="4" /><label for="q4-4" class="fas fa-star"></label>
						            <input type="radio" id="q4-3" name="soru4" value="3" /><label for="q4-3" class="fas fa-star"></label>
						            <input type="radio" id="q4-2" name="soru4" value="2" /><label for="q4-2" class="fas fa-star"></label>
						            <input type="radio" id="q4-1" name="soru4" value="1" /><label for="q4-1" class="fas fa-star"></label>
						        </div>
						    </div>

						    <div class="anket-soru-row mb-4">
						        <label class="soru-metni d-block mb-2">5. Tanı ve tedavi süreci hakkında yeterince bilgilendirildiniz mi?</label>
						        <div class="star-rating">
						            <input type="radio" id="q5-5" name="soru5" value="5" required /><label for="q5-5" class="fas fa-star"></label>
						            <input type="radio" id="q5-4" name="soru5" value="4" /><label for="q5-4" class="fas fa-star"></label>
						            <input type="radio" id="q5-3" name="soru5" value="3" /><label for="q5-3" class="fas fa-star"></label>
						            <input type="radio" id="q5-2" name="soru5" value="2" /><label for="q5-2" class="fas fa-star"></label>
						            <input type="radio" id="q5-1" name="soru5" value="1" /><label for="q5-1" class="fas fa-star"></label>
						        </div>
						    </div>

						    <div class="info-box-panel p-3 mb-4 mt-4 text-center">
							    <h6 class="fw-bold text-primary mb-1">Hızlı Değerlendirme</h6>
							    <p class="small text-muted mb-0">Lütfen aşağıdaki soruları yanıtlayın.</p>
							</div>

						    <div class="soru-container p-3 text-center">
							    <label class="soru-metni d-block mb-3">Randevu saatinize uyuldu mu?</label>
							    <div class="btn-group w-100" role="group">
							        <input type="radio" class="btn-check" name="soru6" id="evet1" value="Evet" required autocomplete="off">
							        <label class="btn btn-outline-turkuaz" for="evet1"><i class="fas fa-check me-2"></i>Evet</label>

							        <input type="radio" class="btn-check" name="soru6" id="hayir1" value="Hayır" autocomplete="off">
							        <label class="btn btn-outline-turkuaz" for="hayir1"><i class="fas fa-times me-2"></i>Hayır</label>
							    </div>
							</div>

						    <div class="soru-container p-3 text-center">
							    <label class="soru-metni d-block mb-3">Aynı doktora tekrar randevu almayı düşünür müsünüz?</label>
							    <div class="btn-group w-100" role="group">
							        <input type="radio" class="btn-check" name="soru7" id="evet2" value="Evet" required autocomplete="off">
							        <label class="btn btn-outline-turkuaz" for="evet2"><i class="fas fa-check me-2"></i>Evet</label>

							        <input type="radio" class="btn-check" name="soru7" id="hayir2" value="Hayır" autocomplete="off">
							        <label class="btn btn-outline-turkuaz" for="hayir2"><i class="fas fa-times me-2"></i>Hayır</label>
							    </div>
							</div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="degerlendir_kaydet" class="btn btn-panel-gonder btn-lg text-white">
                                    Değerlendirmeyi Gönder <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                                <a href="hasta_randevular.php" class="btn btn-link text-muted mt-2 text-decoration-none small">
                                    <i class="fa-solid fa-chevron-left me-1"></i> Vazgeç ve Geri Dön
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>