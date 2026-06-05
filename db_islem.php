<?php
session_start();
require_once("db_baglanti.php");

// PHPMailer Sınıflarını Başlat
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- GENEL MESAJ FONKSİYONU ---
function mesajSet($tur, $icerik, $yonlen) {
    $_SESSION['mesaj'] = ['tur' => $tur, 'icerik' => $icerik];
    header("Location: $yonlen");
    exit();
}

//================================================================================
//                           ADMİN PANELİ
//================================================================================

// 1. POLİKLİNİK EKLEME
if (isset($_POST['pol_ekle'])) {
    $p_ad = $_POST['p_ad'];
    $ekle = $db->prepare("INSERT INTO poliklinikler (p_ad) VALUES (?)")->execute([$p_ad]);
    
    if($ekle) mesajSet('success', 'Yeni poliklinik eklendi.', 'admin_pol_list.php');
    else mesajSet('danger', 'Poliklinik eklenemedi!', 'admin_pol_list.php');
}

// 2. POLİKLİNİK GÜNCELLEME
if (isset($_POST['pol_guncelle'])) {
    $p_id = $_POST['p_id'];
    $p_ad = $_POST['p_ad'];
    $sorgu = $db->prepare("UPDATE poliklinikler SET p_ad = ? WHERE p_id = ?");
    $sonuc = $sorgu->execute([$p_ad, $p_id]);
    
    if($sonuc) mesajSet('success', 'Poliklinik adı güncellendi.', 'admin_pol_list.php');
    else mesajSet('danger', 'Güncelleme başarısız!', 'admin_pol_list.php');
}

// 3. POLİKLİNİK SİLME
if (isset($_GET['pol_sil'])) {
    $p_id = $_GET['pol_sil'];
    // Önce bağlı doktorları sil
    $db->prepare("DELETE FROM doktorlar WHERE poliklinik_id = ?")->execute([$p_id]);
    $sil = $db->prepare("DELETE FROM poliklinikler WHERE p_id = ?")->execute([$p_id]);
    
    if($sil) mesajSet('success', 'Poliklinik ve bağlı doktorlar silindi.', 'admin_pol_list.php');
    else mesajSet('danger', 'Silme işlemi başarısız!', 'admin_pol_list.php');
}

// 4. YENİ DOKTOR EKLEME
if (isset($_POST['doktor_ekle'])) {
    $ad = $_POST['d_ad'];
    $soyad = $_POST['d_soyad'];
    $unvan = $_POST['unvan'];
    $eposta = $_POST['d_eposta'];
    $pol_id = $_POST['poliklinik_id'];

    $rastgele_sifre = rand(10000000, 99999999);
    $hashed_sifre = password_hash($rastgele_sifre, PASSWORD_DEFAULT);

    // Sütun isminin d_eposta mı yoksa e_mail mi olduğuna dikkat et! 
    // Önceki sorgunda e_mail yazıyordu, veritabanına göre kontrol et.
    $sorgu = $db->prepare("INSERT INTO doktorlar (d_ad, d_soyad, unvan, e_mail, sifre, poliklinik_id) VALUES (?, ?, ?, ?, ?, ?)");
    $ekle = $sorgu->execute([$ad, $soyad, $unvan, $eposta, $hashed_sifre, $pol_id]);

    if ($ekle) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hastane.randevu.sistemii@gmail.com';
            $mail->Password = 'tchp icjy jgvy hhdf'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('hastane.randevu.sistemii@gmail.com', 'Hastane Sistemi');
            $mail->addAddress($eposta);
            $mail->isHTML(true);
            $mail->Subject = 'Doktor Paneli Giriş Bilgileriniz';
            $mail->Body = "<h3>Sayın $unvan $ad $soyad,</h3>
                           <p>Sisteme kaydınız yapılmıştır. Giriş bilgileriniz:</p>
                           <ul>
                               <li>E-posta: $eposta</li>
                               <li>Geçici Şifre: <b>$rastgele_sifre</b></li>
                           </ul>";
            $mail->send();
            mesajSet('success', 'Doktor eklendi ve şifresi gönderildi.', 'admin_pol_list.php');
        } catch (Exception $e) {
            mesajSet('warning', 'Doktor eklendi ancak mail gönderilemedi.', 'admin_pol_list.php');
        }
    } else {
        mesajSet('danger', 'Doktor veritabanına kaydedilemedi!', 'admin_pol_list.php');
    }
}

// 5. DOKTOR GÜNCELLEME
if (isset($_POST['dr_guncelle'])) {
    $d_id = $_POST['d_id'];
    $unvan = $_POST['unvan'];
    $ad = $_POST['d_ad'];
    $soyad = $_POST['d_soyad'];
    $eposta = $_POST['d_eposta'];

    $sorgu = $db->prepare("UPDATE doktorlar SET 
        unvan = ?, 
        d_ad = ?, 
        d_soyad = ?, 
        e_mail = ? 
        WHERE d_id = ?");
    
    $sonuc = $sorgu->execute([$unvan, $ad, $soyad, $eposta, $d_id]);

    if($sonuc) mesajSet('success', 'Doktor bilgileri başarıyla güncellendi.', 'admin_pol_list.php');
    else mesajSet('danger', 'Güncelleme yapılamadı!', 'admin_pol_list.php');
}

// 6. DOKTOR SİLME
if (isset($_GET['dr_sil'])) {
    $d_id = $_GET['dr_sil'];
    $sil = $db->prepare("DELETE FROM doktorlar WHERE d_id = ?")->execute([$d_id]);
    
    if($sil) mesajSet('success', 'Doktor kaydı silindi.', 'admin_pol_list.php');
    else mesajSet('danger', 'Doktor silinemedi!', 'admin_pol_list.php');
}

// 7. ADMİN PROFİL GÜNCELLEME
if (isset($_POST['admin_profil_guncelle'])) {
    $a_id = $_SESSION['admin_id'];
    $ad = $_POST['a_ad']; 
    $soyad = $_POST['a_soyad']; 
    $email = $_POST['email'];
    $mevcut_sifre = $_POST['mevcut_sifre']; 
    $yeni_sifre = $_POST['yeni_sifre'];

    $sorgu = $db->prepare("SELECT sifre FROM yoneticiler WHERE a_id = ?");
    $sorgu->execute([$a_id]);
    $admin = $sorgu->fetch();

    if ($admin && password_verify($mevcut_sifre, $admin['sifre'])) {
        $guncel_sifre = !empty($yeni_sifre) ? password_hash($yeni_sifre, PASSWORD_DEFAULT) : $admin['sifre'];
        $guncelle = $db->prepare("UPDATE yoneticiler SET a_ad = ?, a_soyad = ?, email = ?, sifre = ? WHERE a_id = ?");
        $sonuc = $guncelle->execute([$ad, $soyad, $email, $guncel_sifre, $a_id]);
        
        if($sonuc) mesajSet('success', 'Profiliniz güncellendi.', 'admin_profil.php');
        else mesajSet('danger', 'Güncelleme hatası!', 'admin_profil.php');
    } else {
        mesajSet('danger', 'Mevcut şifreniz hatalı!', 'admin_profil.php');
    }
}

// 8. YENİ YÖNETİCİ EKLEME
if (isset($_POST['yeni_admin_ekle'])) {
    $ad = $_POST['a_ad']; $soyad = $_POST['a_soyad']; $email = $_POST['email'];
    $rastgele_sifre = rand(10000000, 99999999);
    $hashed_sifre = password_hash($rastgele_sifre, PASSWORD_DEFAULT);

    $sorgu = $db->prepare("INSERT INTO yoneticiler (a_ad, a_soyad, email, sifre) VALUES (?, ?, ?, ?)");
    $ekle = $sorgu->execute([$ad, $soyad, $email, $hashed_sifre]);

    if ($ekle) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hastane.randevu.sistemii@gmail.com';
            $mail->Password = 'tchp icjy jgvy hhdf';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('hastane.randevu.sistemii@gmail.com', 'Hastane Yönetim Sistemi');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Yönetici Paneli Giriş Bilgileriniz';
            $mail->Body = "<h2>Hoş Geldiniz, $ad $soyad!</h2><p>Geçici Şifreniz: <b>$rastgele_sifre</b></p>";
            $mail->send();
            mesajSet('success', 'Yönetici başarıyla eklendi ve mail gönderildi.', 'admin_yoneticiler.php');
        } catch (Exception $e) {
            mesajSet('warning', 'Yönetici kaydedildi ancak mail gönderilemedi.', 'admin_yoneticiler.php');
        }
    } else {
        mesajSet('danger', 'Yönetici eklenirken bir hata oluştu!', 'admin_yoneticiler.php');
    }
}

// 9. YÖNETİCİ SİLME
if (isset($_GET['admin_sil'])) {
    $a_id = $_GET['admin_sil'];
    if ($a_id == $_SESSION['admin_id']) {
        mesajSet('danger', 'Kendi hesabınızı silemezsiniz!', 'admin_yoneticiler.php');
    }
    $sil = $db->prepare("DELETE FROM yoneticiler WHERE a_id = ?")->execute([$a_id]);
    if($sil) mesajSet('success', 'Yönetici silindi.', 'admin_yoneticiler.php');
    else mesajSet('danger', 'Yönetici silinemedi!', 'admin_yoneticiler.php');
}

//================================================================================
//                           DOKTOR PANELİ
//================================================================================


// 1. PROFİL GÜNCELLEME
if (isset($_POST['doktor_profil_guncelle'])) {
    $d_id = $_SESSION['doctor_id'];
    $ad = $_POST['d_ad']; 
    $soyad = $_POST['d_soyad']; 
    $email = $_POST['e_mail'];
    $mevcut_sifre = $_POST['mevcut_sifre']; 
    $yeni_sifre = $_POST['yeni_sifre'];

    $sorgu = $db->prepare("SELECT sifre FROM doktorlar WHERE d_id = ?");
    $sorgu->execute([$d_id]);
    $doktor = $sorgu->fetch();

    if ($doktor && password_verify($mevcut_sifre, $doktor['sifre'])) {
        $guncel_sifre = !empty($yeni_sifre) ? password_hash($yeni_sifre, PASSWORD_DEFAULT) : $doktor['sifre'];
        $guncelle = $db->prepare("UPDATE doktorlar SET d_ad = ?, d_soyad = ?, e_mail = ?, sifre = ? WHERE d_id = ?");
        $sonuc = $guncelle->execute([$ad, $soyad, $email, $guncel_sifre, $d_id]);
        
        if($sonuc) mesajSet('success', 'Profiliniz güncellendi.', 'doktor_profil.php');
        else mesajSet('danger', 'Güncelleme hatası!', 'doktor_profil.php');
    } else {
        mesajSet('danger', 'Mevcut şifreniz hatalı!', 'doktor_profil.php');
    }
}

// 2. RANDEVU DURUMU GÜNCELLEME
if (isset($_GET['randevu_id']) && isset($_GET['yeni_durum'])) {
    $r_id = $_GET['randevu_id'];
    $yeni_durum = $_GET['yeni_durum'];

    $izin_verilenler = ['Geldi', 'Gelmedi', 'Bekliyor'];
    
    if (in_array($yeni_durum, $izin_verilenler)) {
        $sorgu = $db->prepare("UPDATE randevular SET randevu_durum = ? WHERE randevu_id = ?");
        $guncelle = $sorgu->execute([$yeni_durum, $r_id]);

        if ($guncelle) {
            $_SESSION['mesaj'] = [
                'tur' => 'success',
                'icerik' => 'Randevu durumu güncellendi: ' . $yeni_durum
            ];
        } else {
            $_SESSION['mesaj'] = [
                'tur' => 'danger',
                'icerik' => 'Veritabanı hatası! Güncelleme yapılamadı.'
            ];
        }
    } else {
        $_SESSION['mesaj'] = [
            'tur' => 'danger',
            'icerik' => 'Geçersiz işlem denemesi!'
        ];
    }
    header("Location: doktor_randevular.php");
    exit();
}






//================================================================================
//                           HASTA PANELİ
//================================================================================

// 1. PROFİL GÜNCELLEME
if (isset($_POST['hasta_profil_guncelle'])) {
    $h_id = $_SESSION['user_id'];
    $ad = $_POST['h_ad']; 
    $soyad = $_POST['h_soyad']; 
    $email = $_POST['email'];
    $mevcut_sifre = $_POST['mevcut_sifre']; 
    $yeni_sifre = $_POST['yeni_sifre'];

    $sorgu = $db->prepare("SELECT sifre FROM hastalar WHERE h_id = ?");
    $sorgu->execute([$h_id]);
    $hasta = $sorgu->fetch();

    if ($hasta && password_verify($mevcut_sifre, $hasta['sifre'])) {
        $guncel_sifre = !empty($yeni_sifre) ? password_hash($yeni_sifre, PASSWORD_DEFAULT) : $hasta['sifre'];
        $guncelle = $db->prepare("UPDATE hastalar SET h_ad = ?, h_soyad = ?, email = ?, sifre = ? WHERE h_id = ?");
        $sonuc = $guncelle->execute([$ad, $soyad, $email, $guncel_sifre, $h_id]);
        
        if($sonuc) mesajSet('success', 'Profiliniz güncellendi.', 'hasta_profil.php');
        else mesajSet('danger', 'Güncelleme hatası!', 'hasta_profil.php');
    } else {
        mesajSet('danger', 'Mevcut şifreniz hatalı!', 'hasta_profil.php');
    }
}

// 2. RANDEVU İPTAL ETME
if (isset($_GET['iptal_id'])) {
    $randevu_id = $_GET['iptal_id'];
    $hasta_id = $_SESSION['user_id'];
    $durum = "İptal Edildi";

    $sorgu = $db->prepare("UPDATE randevular SET randevu_durum = ? WHERE hasta_id = ? and randevu_id = ?");
    $sonuc = $sorgu->execute([$durum, $hasta_id, $randevu_id]);

    if($sonuc){
        mesajSet('success', 'Randevunuz iptal edildi.', 'hasta_randevular.php');
    }
    else {
        mesajSet('danger', 'İptal işlemi sırasında bir hata oluştu.', 'hasta_randevular.php');
    }
}

// 3. RANDEVU ALMA
if(isset($_POST['randevu_al'])) {
    // Session isimlerini senin düzelttiğin haliyle kullanıyoruz
    $hasta_id = $_SESSION['user_id']; 
    $doktor_id = $_POST['doktor_id'];
    $poliklinik_id = $_POST['poliklinik_id']; // ID'yi de alalım, DB'de olması gerekir
    $tarih = $_POST['secilen_tarih'];
    $saat = $_POST['secilen_saat'];
    $durum = "Bekliyor";

    // Boş veri kontrolü
    if(empty($doktor_id) || empty($tarih) || empty($saat) || empty($poliklinik_id)) {
        header("Location: hasta_randevu_al.php?durum=eksik_veri");
        exit();
    }

    // Çakışma kontrolü
    $kontrol = $db->prepare("SELECT * FROM randevular WHERE doktor_id = ? AND randevu_tarihi = ? AND randevu_saati = ?");
    $kontrol->execute([$doktor_id, $tarih, $saat]);

    if($kontrol->rowCount() > 0) {
        header("Location: hasta_randevu_al.php?durum=dolu");
        exit();
    }

    $ekle = $db->prepare("INSERT INTO randevular (hasta_id, doktor_id, randevu_tarihi, randevu_saati, randevu_durum) VALUES (?,?,?,?,?)");
    $sonuc = $ekle->execute([$hasta_id, $doktor_id, $tarih, $saat, $durum]);

    if($sonuc) {
        $p_ad = $_POST['poliklinik_ad'];
        $d_ad = $_POST['doktor_ad'];

        // Başarılı yönlendirme
        header("Location: hasta_randevu_al.php?mesaj=basarili&tar=$tarih&saat=$saat&pol=" . urlencode($p_ad) . "&dok=" . urlencode($d_ad));
        exit();
    } else {
        header("Location: hasta_randevu_al.php?durum=hata");
        exit();
    }
}

// 4. RANDEVU DEĞERLENDİRME KAYIT
if (isset($_POST['degerlendir_kaydet'])) {
    // Çıktı tamponunu temizle
    if (ob_get_level()) ob_end_clean();
    ob_start();

    $randevu_id = $_POST['randevu_id'];
    $s1 = $_POST['soru1'];
    $s2 = $_POST['soru2'];
    $s3 = $_POST['soru3'];
    $s4 = $_POST['soru4'];
    $s5 = $_POST['soru5'];
    $s6 = $_POST['soru6'];
    $s7 = $_POST['soru7'];

    try {
        $sql = "INSERT INTO degerlendirmeler (randevu_id, soru1, soru2, soru3, soru4, soru5, soru6, soru7) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $sorgu = $db->prepare($sql);
        $kaydet = $sorgu->execute([$randevu_id, $s1, $s2, $s3, $s4, $s5, $s6, $s7]);

        if ($kaydet) {
            // Yönlendirmeden önce hiçbir şeyin ekrana yazılmadığından emin oluyoruz
            ob_clean(); 
            header("Location: hasta_randevular.php?durum=ok&islem=degerlendirme");
            // PHP Header başarısız olursa JS devreye girer:
            echo '<script>window.location.href="hasta_randevular.php?durum=ok&islem=degerlendirme";</script>';
            exit();
        } else {
            header("Location: hasta_randevular.php?durum=no");
            echo '<script>window.location.href="hasta_randevular.php?durum=no";</script>';
            exit();
        }
    } catch (PDOException $e) {
        header("Location: hasta_randevular.php?durum=no");
        exit();
    }
}
// Butona basılmadan gelindiyse anasayfaya at (Kodun en altında durabilir)
header("Location: anasayfa.php");
exit();
