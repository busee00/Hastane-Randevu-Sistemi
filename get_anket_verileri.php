<?php
require_once("db_baglanti.php");

if (isset($_GET['p_id'])) {
    $p_id = intval($_GET['p_id']);
    $ay   = isset($_GET['ay'])  ? intval($_GET['ay'])  : intval(date('n'));
    $yil  = isset($_GET['yil']) ? intval($_GET['yil']) : intval(date('Y'));

    $sorgu = $db->prepare("
        SELECT
            dr.d_id,
            CONCAT(dr.unvan, ' ', dr.d_ad, ' ', dr.d_soyad) AS tam_ad,
            COALESCE(istatistik.s1, 0) AS soru1,
            COALESCE(istatistik.s2, 0) AS soru2,
            COALESCE(istatistik.s3, 0) AS soru3,
            COALESCE(istatistik.s4, 0) AS soru4,
            COALESCE(istatistik.s5, 0) AS soru5,
            COALESCE(istatistik.s6, 0) AS soru6,
            COALESCE(istatistik.s7, 0) AS soru7
        FROM doktorlar dr
        LEFT JOIN (
            SELECT
                r.doktor_id,
                AVG(deg.soru1) * 20                                        AS s1,
                AVG(deg.soru2) * 20                                        AS s2,
                AVG(deg.soru3) * 20                                        AS s3,
                AVG(deg.soru4) * 20                                        AS s4,
                AVG(deg.soru5) * 20                                        AS s5,
                AVG(CASE WHEN deg.soru6 = 'Evet' THEN 1 ELSE 0 END) * 100 AS s6,
                AVG(CASE WHEN deg.soru7 = 'Evet' THEN 1 ELSE 0 END) * 100 AS s7
            FROM degerlendirmeler deg
            INNER JOIN randevular r ON deg.randevu_id = r.randevu_id
            WHERE MONTH(r.randevu_tarihi) = :ay
              AND YEAR(r.randevu_tarihi)  = :yil
            GROUP BY r.doktor_id
        ) AS istatistik ON dr.d_id = istatistik.doktor_id
        WHERE dr.poliklinik_id = :p_id
    ");

    $sorgu->execute([':p_id' => $p_id, ':ay' => $ay, ':yil' => $yil]);
    $veriler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($veriler);
    exit;
}