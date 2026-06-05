<?php
require_once("db_baglanti.php");
session_start();

$doktor_id = intval($_GET['doktor_id'] ?? 0);

if (!$doktor_id) {
    echo json_encode([]);
    exit;
}

$sonuclar = [];

$ayAdlari = ['', 'Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz',
                  'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];

for ($i = 5; $i >= 0; $i--) {
    $hedefAy  = date('n', strtotime("-$i months"));
    $hedefYil = date('Y', strtotime("-$i months"));

    $stmt = $db->prepare("
        SELECT
            AVG(dgr.soru1) * 20                                        AS soru1,
            AVG(dgr.soru2) * 20                                        AS soru2,
            AVG(dgr.soru3) * 20                                        AS soru3,
            AVG(dgr.soru4) * 20                                        AS soru4,
            AVG(dgr.soru5) * 20                                        AS soru5,
            AVG(CASE WHEN dgr.soru6 = 'Evet' THEN 1 ELSE 0 END) * 100 AS soru6,
            AVG(CASE WHEN dgr.soru7 = 'Evet' THEN 1 ELSE 0 END) * 100 AS soru7,
            COUNT(*)                                                   AS degerlendirme_sayisi
        FROM degerlendirmeler dgr
        INNER JOIN randevular r ON r.randevu_id = dgr.randevu_id
        WHERE r.doktor_id            = :doktor_id
          AND MONTH(r.randevu_tarihi) = :ay
          AND YEAR(r.randevu_tarihi)  = :yil
    ");

    $stmt->execute([
        ':doktor_id' => $doktor_id,
        ':ay'        => $hedefAy,
        ':yil'       => $hedefYil
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['degerlendirme_sayisi'] == 0) continue;

    $sonuclar[] = [
        'ay_etiket' => $ayAdlari[$hedefAy] . ' ' . $hedefYil,
        'soru1'     => $row['soru1'] !== null ? round($row['soru1'], 1) : null,
        'soru2'     => $row['soru2'] !== null ? round($row['soru2'], 1) : null,
        'soru3'     => $row['soru3'] !== null ? round($row['soru3'], 1) : null,
        'soru4'     => $row['soru4'] !== null ? round($row['soru4'], 1) : null,
        'soru5'     => $row['soru5'] !== null ? round($row['soru5'], 1) : null,
        'soru6'     => $row['soru6'] !== null ? round($row['soru6'], 1) : null,
        'soru7'     => $row['soru7'] !== null ? round($row['soru7'], 1) : null,
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($sonuclar);