<?php 
session_start();
require_once("db_baglanti.php");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yönetici Paneli - Randevular</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style_panel.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php require_once("panel_menu_admin.php"); ?>

    <div class="container-fluid mt-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="admin_randevular.php" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold">Zaman Filtresi:</label>
                    </div>
                    <div class="col-auto">
                        <?php $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'bugun'; ?>
                        <select name="filtre" class="form-select">
                            <option value="bugun" <?php echo ($filtre == 'bugun') ? 'selected' : ''; ?>>Bugün</option>
                            <option value="son_hafta" <?php echo ($filtre == 'son_hafta') ? 'selected' : ''; ?>>Son 1 Hafta</option>
                            <option value="gelecek_hafta" <?php echo ($filtre == 'gelecek_hafta') ? 'selected' : ''; ?>>Gelecek 1 Hafta</option>
                            <option value="bu_ay" <?php echo ($filtre == 'bu_ay') ? 'selected' : ''; ?>>Bu Ay</option>
                            <option value="hepsi" <?php echo ($filtre == 'hepsi') ? 'selected' : ''; ?>>Tüm Zamanlar</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filtrele</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $where_condition = "";
        switch ($filtre) {
            case 'bugun':
                $where_condition = "WHERE r.randevu_tarihi = CURDATE()";
                break;
            case 'son_hafta':
                $where_condition = "WHERE r.randevu_tarihi BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()";
                break;
            case 'gelecek_hafta':
                $where_condition = "WHERE r.randevu_tarihi BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'bu_ay':
                $where_condition = "WHERE MONTH(r.randevu_tarihi) = MONTH(CURDATE()) AND YEAR(r.randevu_tarihi) = YEAR(CURDATE())";
                break;
            case 'hepsi':
                $where_condition = ""; 
                break;
        }

        $sorguText = "SELECT 
                    r.randevu_id, r.randevu_tarihi, r.randevu_saati, r.randevu_durum,
                    h.h_id, h.h_ad, h.h_soyad, 
                    d.d_ad, d.d_soyad, d.unvan,
                    p.p_ad
                  FROM randevular r
                  LEFT JOIN hastalar h ON r.hasta_id = h.h_id
                  LEFT JOIN doktorlar d ON r.doktor_id = d.d_id
                  LEFT JOIN poliklinikler p ON d.poliklinik_id = p.p_id
                  $where_condition 
                  ORDER BY r.randevu_tarihi DESC, r.randevu_saati ASC";

        try {
            // PDO ile sorgu çalıştırma
            $sorgu = $db->query($sorguText);
            $randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("<div class='alert alert-danger'>Sorgu Hatası: " . $e->getMessage() . "</div>");
        }
        ?>

        <div class="table-responsive bg-white p-3 rounded shadow-sm border">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Hasta ID</th>
                        <th>Ad Soyad</th>
                        <th>Poliklinik</th>
                        <th>Doktor</th>
                        <th>Tarih</th>
                        <th>Saat</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($randevular) == 0): ?>
                        <tr><td colspan="8" class="text-center py-4">Gösterilecek randevu bulunamadı.</td></tr>
                    <?php endif; ?>

                    <?php 
                    	$sira = 1;
                    	foreach($randevular as $row): 
                        $badge = "bg-secondary";
                        $durum = mb_strtolower($row['randevu_durum'], 'UTF-8');
                        if($durum == "bekliyor") $badge = "bg-warning text-dark";
                        elseif($durum == "tamamlandı") $badge = "bg-success";
                        elseif($durum == "iptal") $badge = "bg-danger";
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $sira; ?></td>
                        <td><?php echo $row['h_id']; ?></td>
                        <td><?php echo $row['h_ad'] . " " . $row['h_soyad']; ?></td>
                        <td><?php echo $row['p_ad']; ?></td>
                        <td><?php echo $row['unvan'] . " " . $row['d_ad'] . " " . $row['d_soyad']; ?></td>
                        <td><?php echo date("d.m.Y", strtotime($row['randevu_tarihi'])); ?></td>
                        <td><?php echo substr($row['randevu_saati'], 0, 5); ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($row['randevu_durum']); ?></span></td>
                    </tr>
                    <?php 
                    	$sira++; 
                		endforeach; 
                	?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>