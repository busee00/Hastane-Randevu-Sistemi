<?php
error_reporting(0);
require_once("db_baglanti.php");

//================================================================================
//                           HASTA PANELİ/RANDEVU ALMA
//================================================================================

// 1. DOKTORLARI GETİR
if(isset($_POST['islem']) && $_POST['islem'] == 'doktor_getir') {
    $p_id = $_POST['pol_id'];
    $sorgu = $db->prepare("SELECT * FROM doktorlar WHERE poliklinik_id = ?");
    $sorgu->execute([$p_id]);
    echo '<option value="">Doktor Seçiniz...</option>';
    while($row = $sorgu->fetch()) {
        echo "<option value='".$row['d_id']."'>".$row['d_ad']." ".$row['d_soyad']."</option>";
    }
}

// 2. DOLU SAATLERİ GETİR
if(isset($_POST['islem']) && $_POST['islem'] == 'dolu_saatleri_getir') {
    $d_id = $_POST['doktor_id'];
    $tarih = $_POST['tarih'];

    // TIME_FORMAT kullanarak saati SS:DD (09:00) formatına indirgiyoruz
    $sorgu = $db->prepare("SELECT TIME_FORMAT(randevu_saati, '%H:%i') as saat FROM randevular WHERE doktor_id = ? AND randevu_tarihi = ?");
    $sorgu->execute([$d_id, $tarih]);
    
    // fetchAll ile direkt sütunu dizi olarak alıyoruz
    $dolu_saatler = $sorgu->fetchAll(PDO::FETCH_COLUMN);
    
    // Eğer veritabanı boşsa JS hata almasın diye boş dizi garantisi
    if(!$dolu_saatler) $dolu_saatler = [];
    
    echo json_encode($dolu_saatler);
    exit; // Başka çıktı basılmasın diye sonlandırıyoruz
}

//================================================================================
//                           ADMİN PANELİ/DOKTOR FİLTRELEME
//================================================================================


if (isset($_POST['poliklinik_id'])) 
{
    $p_id = $_POST['poliklinik_id'];

    // Seçilen polikliniğe ait doktorları getir
    $sorgu = $db->prepare("SELECT d_id, d_ad, d_soyad FROM doktorlar WHERE poliklinik_id = ?");
    $sorgu->execute([$p_id]);

    echo "<option value=''>Doktor Seçiniz...</option>";

    while ($row = $sorgu->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='".$row['d_id']."'>".$row['d_ad']." ".$row['d_soyad']."</option>";
    }
}
?>