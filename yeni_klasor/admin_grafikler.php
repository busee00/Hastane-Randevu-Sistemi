<?php 
require_once("db_baglanti.php");
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yönetici Paneli - Grafikler</title>

    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style_panel.css">
    <link rel="stylesheet" type="text/css" href="admin_grafikler.css">

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php require_once("panel_menu_admin.php"); ?>

    <!-- Filtreleme Alanı -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <div class="row align-items-center g-2">
                <div class="col-md-4">
                    <label class="form-label filter-label">
                        <i class="fas fa-hospital me-1"></i> Poliklinik
                    </label>
                    <select id="poliklinikSec" class="form-select custom-input">
                        <option value="">Poliklinik Seçiniz...</option>
                        <?php
                            $sorgu = $db->query("SELECT * FROM poliklinikler");
                            while($row = $sorgu->fetch()) {
                                echo "<option value='".$row['p_id']."'>".$row['p_ad']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label filter-label">
                        <i class="fas fa-calendar-alt me-1"></i> Ay
                    </label>
                    <select id="aySec" class="form-select custom-input">
                        <option value="1">Ocak</option>
                        <option value="2">Şubat</option>
                        <option value="3">Mart</option>
                        <option value="4">Nisan</option>
                        <option value="5">Mayıs</option>
                        <option value="6">Haziran</option>
                        <option value="7">Temmuz</option>
                        <option value="8">Ağustos</option>
                        <option value="9">Eylül</option>
                        <option value="10">Ekim</option>
                        <option value="11">Kasım</option>
                        <option value="12">Aralık</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label filter-label">
                        <i class="fas fa-calendar me-1"></i> Yıl
                    </label>
                    <select id="yilSec" class="form-select custom-input">
                        <?php
                            $mevcutYil = date('Y');
                            for ($y = $mevcutYil; $y >= $mevcutYil - 4; $y--) {
                                echo "<option value='$y'>$y</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button onclick="verileriGetir()" class="btn btn-primary custom-btn w-100">
                        <i class="fas fa-chart-bar me-1"></i> Seç
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Alanı -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <button onclick="oncekiSoru()" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i> Geri
            </button>
            <h5 id="soruBasligi" class="mb-0">Memnuniyet Grafikleri</h5>
            <button onclick="sonrakiSoru()" class="btn btn-sm btn-outline-secondary">
                İleri <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="card-body" style="overflow-x: auto; width: 100%;">
            <div id="grafikKonteyner" style="width: 100%; height: 400px;">
                <canvas id="anketGrafik"></canvas>
            </div>
        </div>
    </div>

    <!-- Doktor Detay Modalı -->
    <div class="modal fade" id="doktorDetayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="detayDoktorAdi">Doktor Detayları</h5>
                        <small class="text-muted">Performans analizi</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Sekme Başlıkları -->
                    <ul class="nav nav-tabs px-3 pt-2" id="detayTablar" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-mevcut" data-bs-toggle="tab"
                                    data-bs-target="#panel-mevcut" type="button" role="tab">
                                <i class="fas fa-chart-bar me-1"></i> Mevcut Dönem Skorları
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-trend" data-bs-toggle="tab"
                                    data-bs-target="#panel-trend" type="button" role="tab">
                                <i class="fas fa-chart-line me-1"></i> Son 6 Aylık Trend
                            </button>
                        </li>
                    </ul>

                    <!-- Sekme İçerikleri -->
                    <div class="tab-content p-3" id="detayTabIcerik">
                        <!-- Mevcut Dönem Sekmesi -->
                        <div class="tab-pane fade show active" id="panel-mevcut" role="tabpanel">
                            <canvas id="detayGrafik" height="280"></canvas>
                        </div>

                        <!-- Trend Sekmesi -->
                        <div class="tab-pane fade" id="panel-trend" role="tabpanel">
                            <div id="trendBosUyari" class="alert alert-info d-none" style="display:none!important">
                                <i class="fas fa-info-circle me-1"></i>
                                Bu doktor için yeterli geçmiş veri bulunamadı.
                            </div>
                            <div style="position:relative; height:320px;">
                                <canvas id="trendGrafik"></canvas>
                            </div>
                            <p class="text-muted text-center mt-2" style="font-size:0.8rem;">
                                <i class="fas fa-info-circle me-1"></i>
                                Her renk bir memnuniyet sorusunu temsil etmektedir. Grafiğin üzerine gelerek detayları inceleyebilirsiniz.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="admin_grafikler.js"></script>
</body>
</html>
