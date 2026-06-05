<?php 
session_start();
require_once("db_baglanti.php");

// Poliklinikleri çek
$pol_sorgu = $db->query("SELECT * FROM poliklinikler ORDER BY p_ad ASC");
$poliklinikler = $pol_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Poliklinik ve Doktor Yönetimi</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <?php include("panel_menu_admin.php"); ?>

    <div class="container mt-5">
        <?php if(isset($_GET['durum'])): ?>
        <div class="alert alert-<?php echo $_GET['durum'] == 'ok' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_GET['durum'] == 'ok' ? 'İşlem başarıyla tamamlandı.' : 'Bir hata oluştu!'; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fa-solid fa-hospital text-primary"></i> Poliklinik & Doktor Yönetimi</h2>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#polEkleModal">
                    <i class="fa fa-plus"></i> Yeni Poliklinik
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doktorEkleModal">
                    <i class="fa fa-user-md"></i> Yeni Doktor
                </button>
            </div>
        </div>

        <div class="accordion shadow-sm" id="poliklinikAkordiyon">
            <?php foreach($poliklinikler as $pol): 
                $p_id = $pol['p_id'];
                $doktor_sorgu = $db->prepare("SELECT * FROM doktorlar WHERE poliklinik_id = ?");
                $doktor_sorgu->execute([$p_id]);
                $doktorlar = $doktor_sorgu->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $p_id; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $p_id; ?>">
                        <span class="badge doctor-count-badge me-3">
                            <i class="fa-solid fa-user-doctor me-1"></i> <?php echo count($doktorlar); ?>
                        </span>
                        <strong><?php echo $pol['p_ad']; ?></strong>
                    </button>
                </h2>
                <div id="collapse<?php echo $p_id; ?>" class="accordion-collapse collapse" data-bs-parent="#poliklinikAkordiyon">
                    <div class="accordion-body bg-white">
                        <div class="mb-3 d-flex justify-content-end border-bottom pb-2">
                            <button class="btn btn-sm btn-outline-warning me-2" onclick="polDuzenle(<?php echo $p_id; ?>, '<?php echo $pol['p_ad']; ?>')">
                                <i class="fa fa-edit"></i> Polikliniği Düzenle
                            </button>
                            <a href="db_islem.php?pol_sil=<?php echo $p_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu polikliniği ve bağlı doktorları silmek istediğinize emin misiniz?')">
                                <i class="fa fa-trash"></i> Sil
                            </a>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Unvan</th>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($doktorlar)): ?>
                                    <tr><td colspan="4" class="text-center text-muted small">Bu poliklinikte henüz doktor tanımlı değil.</td></tr>
                                <?php else: ?>
                                    <?php foreach($doktorlar as $dr): ?>
                                    <tr>
                                        <td><?php echo $dr['unvan']; ?></td>
                                        <td><?php echo $dr['d_ad'] . " " . $dr['d_soyad']; ?></td>
                                        <td><?php echo $dr['d_eposta']; ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-info text-white" 
                                                onclick="drDuzenle(<?php echo $dr['d_id']; ?>, '<?php echo $dr['unvan']; ?>', '<?php echo $dr['d_ad']; ?>', '<?php echo $dr['d_soyad']; ?>', '<?php echo $dr['d_eposta']; ?>')" 
                                                title="Düzenle">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="db_islem.php?dr_sil=<?php echo $dr['d_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Doktoru silmek istediğinize emin misiniz?')"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="polDuzenleModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="db_islem.php" method="POST" class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Poliklinik Güncelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="p_id" id="edit_p_id">
                    <label class="form-label fw-bold">Poliklinik Adı</label>
                    <input type="text" name="p_ad" id="edit_p_ad" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="pol_guncelle" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="drDuzenleModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="db_islem.php" method="POST" class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Doktor Bilgilerini Güncelle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="d_id" id="edit_d_id">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Unvan</label>
                            <select name="unvan" id="edit_unvan" class="form-select">
                                <option value="Prof. Dr.">Prof. Dr.</option>
                                <option value="Doç. Dr.">Doç. Dr.</option>
                                <option value="Op. Dr.">Op. Dr.</option>
                                <option value="Uzman Dr.">Uzman Dr.</option>
                                <option value="Dr.">Dr.</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ad</label>
                            <input type="text" name="d_ad" id="edit_d_ad" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Soyad</label>
                            <input type="text" name="d_soyad" id="edit_d_soyad" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">E-posta</label>
                            <input type="email" name="d_eposta" id="edit_d_eposta" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="dr_guncelle" class="btn btn-info text-white">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="polEkleModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="db_islem.php" method="POST" class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Yeni Poliklinik Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-bold">Poliklinik Adı</label>
                    <input type="text" name="p_ad" class="form-control" placeholder="Örn: Göğüs Hastalıkları" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="pol_ekle" class="btn btn-success">Polikliniği Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="doktorEkleModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="db_islem.php" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-user-md"></i> Yeni Doktor Kaydı</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Unvan</label>
                            <select name="unvan" class="form-select" required>
                                <option value="Prof. Dr.">Prof. Dr.</option>
                                <option value="Doç. Dr.">Doç. Dr.</option>
                                <option value="Op. Dr.">Op. Dr.</option>
                                <option value="Uzman Dr.">Uzman Dr.</option>
                                <option value="Dr.">Dr.</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ad</label>
                            <input type="text" name="d_ad" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Soyad</label>
                            <input type="text" name="d_soyad" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Poliklinik Seçin</label>
                            <select name="poliklinik_id" class="form-select" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach($poliklinikler as $p): ?>
                                    <option value="<?php echo $p['p_id']; ?>"><?php echo $p['p_ad']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">E-posta</label>
                            <input type="email" name="d_eposta" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="doktor_ekle" class="btn btn-primary">Doktoru Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Poliklinik Verilerini Modal'a Basar
    function polDuzenle(id, ad) {
        document.getElementById('edit_p_id').value = id;
        document.getElementById('edit_p_ad').value = ad;
        var myModal = new bootstrap.Modal(document.getElementById('polDuzenleModal'));
        myModal.show();
    }

    // Doktor Verilerini Modal'a Basar
    function drDuzenle(id, unvan, ad, soyad, eposta) {
        document.getElementById('edit_d_id').value = id;
        document.getElementById('edit_unvan').value = unvan;
        document.getElementById('edit_d_ad').value = ad;
        document.getElementById('edit_d_soyad').value = soyad;
        document.getElementById('edit_d_eposta').value = eposta;
        var drModal = new bootstrap.Modal(document.getElementById('drDuzenleModal'));
        drModal.show();
    }
    </script>
</body>
</html>