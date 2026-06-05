<?php 
require_once("db_baglanti.php");
session_start();

$doktor_id = $_GET['doktor_id'];

$sorgu = $db->prepare("SELECT dgr.*
                       FROM degerlendirmeler dgr
                       JOIN randevular r ON r.randevu_id = dgr.randevu_id
                       JOIN doktorlar d ON r.doktor_id = d.d_id 
                       WHERE r.doktor_id = ?");
$sorgu->execute([$doktor_id]);
$degerlendirmeler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

$analizler  = [];

for ($i=0; $i<5 ; $i++) 
{ 
	$toplam = 0;
	$n = 0;
	foreach ($degerlendirmeler as $degerlendirme) 
	{
		$sutun = "soru" . ($i+1); 
		$toplam +=  $degerlendirme[$sutun];	
		$n++;
	}
	if ($n>0) 
	{
		$analizler[$i] = ($toplam / $n)*20;
	}
}

for ($j=5; $j<7 ; $j++) 
{ 
	$e = 0;
	$n = 0;
	foreach ($degerlendirmeler as $degerlendirme) 
	{
		$sutun = "soru" . ($j+1); 
		if ($degerlendirme[$sutun] == "Evet") 
		{
			$e++;
		} 
		$n++;
	}
	if ($n>0) 
	{
		$analizler[$j] = ($e / $n)*100; // Evet'in yüzde oranı
	}
}

echo "<pre>";
print_r($analizler);
echo "</pre>";


?>