// Global Değişkenler
let anketGrafigi = null; 
let tumVeriler = [];
let aktifSoruIndex = 1;
let detayChart = null;
let trendChart = null;

// Soru metinleri
const soruMetinleri = {
    1: "İletişim ve Nezaket Performansı",
    2: "Hasta Dinleme ve Empati",
    3: "Ayırılan Sürenin Yeterliliği",
    4: "Açıklamaların Anlaşılırlığı",
    5: "Tanı/Tedavi Bilgilendirmesi",
    6: "Randevu Saatine Uyum",
    7: "Tekrar Tercih Etme"
};

// Sayfa yüklendiğinde mevcut ay/yıl değerlerini ayarla
document.addEventListener('DOMContentLoaded', function() {
    const simdi = new Date();
    document.getElementById('aySec').value = simdi.getMonth() + 1; // 1-12
    document.getElementById('yilSec').value = simdi.getFullYear();
});

// 1. VERİ ÇEKME FONKSİYONU
function verileriGetir() {
    const p_id = document.getElementById('poliklinikSec').value;
    const ay   = document.getElementById('aySec').value;
    const yil  = document.getElementById('yilSec').value;

    if (!p_id) {
        alert("Lütfen bir poliklinik seçin!");
        return;
    }

    fetch(`get_anket_verileri.php?p_id=${p_id}&ay=${ay}&yil=${yil}`)
        .then(response => response.json())
        .then(data => {
            tumVeriler = data;
            aktifSoruIndex = 1;
            grafikOlustur(aktifSoruIndex);
        })
        .catch(error => console.error('Hata:', error));
}

// 2. GRAFİK OLUŞTURMA/GÜNCELLEME FONKSİYONU
function grafikOlustur(soruNo) {
    if (tumVeriler.length == 0) {
        alert("Bu poliklinikte seçilen dönemde değerlendirme yapılmış bir randevu bulunmuyor.");
        if (anketGrafigi) anketGrafigi.destroy();
        return;
    }

    const canvas = document.getElementById('anketGrafik');
    const ctx = canvas.getContext('2d');
    const grafikKonteyner = document.getElementById('grafikKonteyner');
    
    const doktorSayisi = tumVeriler.length;
    const sutunBasinaAlan = 100;
    const hesaplananGenislik = Math.max(grafikKonteyner.offsetWidth, doktorSayisi * sutunBasinaAlan);
    canvas.parentElement.style.width = hesaplananGenislik + "px";

    const etiketler = tumVeriler.map(item => item.tam_ad);
    const degerler  = tumVeriler.map(item => item['soru' + soruNo]);

    document.getElementById('soruBasligi').innerText = soruMetinleri[soruNo];

    if (anketGrafigi) anketGrafigi.destroy();

    anketGrafigi = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiketler,
            datasets: [{
                label: 'Memnuniyet Yüzdesi (%)',
                data: degerler,
                backgroundColor: 'rgba(46, 180, 209, 0.7)',
                borderColor: 'rgb(46, 180, 209)',
                borderWidth: 1,
                hoverBackgroundColor: 'rgba(46, 180, 209, 0.9)',
                barThickness: 60,
                maxBarThickness: 50,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const secilenDoktor = tumVeriler[index];
                    doktorDetayiniGoster(secilenDoktor);
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: (value) => '%' + value }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 11 }
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                tooltip: { enabled: true },
                legend: { display: false }
            }
        }
    });
}

// 3. DOKTOR DETAY FONKSİYONU
function doktorDetayiniGoster(doktor) {
    const modalElement = new bootstrap.Modal(document.getElementById('doktorDetayModal'));
    document.getElementById('detayDoktorAdi').innerText = doktor.tam_ad;
    modalElement.show();

    setTimeout(() => {
        // Mevcut Skorlar sekmesi grafiği
        const detayCtx = document.getElementById('detayGrafik').getContext('2d');
        if (detayChart) detayChart.destroy();

        const detayVerileri = [
            doktor.soru1, doktor.soru2, doktor.soru3,
            doktor.soru4, doktor.soru5, doktor.soru6, doktor.soru7
        ];

        detayChart = new Chart(detayCtx, {
            type: 'bar',
            data: {
                labels: [
                    "İletişim ve Nezaket", "Hasta Dinleme", "Süre Yeterliliği",
                    "Açıklama Anlaşılırlığı", "Tanı/Tedavi Bilgisi", "Randevu Saati", "Tekrar Tercih"
                ],
                datasets: [{
                    label: 'Memnuniyet (%)',
                    data: detayVerileri,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1,
                    hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                scales: { x: { beginAtZero: true, max: 100 } },
                plugins: { legend: { display: false } }
            }
        });

        // Trend sekmesi için veriyi çek
        trendVerisiniGetir(doktor.d_id);
    }, 200);
}

// 4. TREND VERİSİ ÇEKME
function trendVerisiniGetir(doktorId) {
    const p_id = document.getElementById('poliklinikSec').value;

    fetch(`get_trend_verileri.php?doktor_id=${doktorId}`)
        .then(r => r.json())
        .then(data => {
            trendGrafiginiOlustur(data);
        })
        .catch(err => console.error('Trend verisi hatası:', err));
}

// 5. TREND GRAFİĞİ OLUŞTURMA
function trendGrafiginiOlustur(data) {
    const trendCtx = document.getElementById('trendGrafik').getContext('2d');
    if (trendChart) trendChart.destroy();

    if (!data || data.length === 0) {
        document.getElementById('trendBosUyari').style.display = 'block';
        return;
    }
    document.getElementById('trendBosUyari').style.display = 'none';

    // data = [{ay_etiket: "Ara 2024", ort_skor: 78.5}, ...]
    const etiketler = data.map(d => d.ay_etiket);

    // Her soru için ayrı dataset
    // Her soru için renk, çizgi stili ve nokta şekli ayrı ayrı tanımlandı
    const soruStilleri = [
        { renk: '#f5ef40', dash: [], nokta: 'circle'   }, // soru1 - kırmızı
        { renk: '#2196f3', dash: [], nokta: 'rect'     }, // soru2 - mavi
        { renk: '#ff9800', dash: [], nokta: 'triangle' }, // soru3 - turuncu
        { renk: '#4caf50', dash: [], nokta: 'rectRot'  }, // soru4 - yeşil
        { renk: '#9c27b0', dash: [], nokta: 'circle'   }, // soru5 - mor
        { renk: '#1f1fa6', dash: [], nokta: 'rect'     }, // soru6 - teal
        { renk: '#f44336', dash: [], nokta: 'star' }, // soru7 - koyu kırmızı
    ];

    const datasets = Object.entries(soruMetinleri).map(([soruNo, baslik], i) => {
        const stil = soruStilleri[i];
        return {
            label: baslik,
            data: data.map(d => d['soru' + soruNo] ?? null),
            borderColor: stil.renk,
            backgroundColor: stil.renk + '18', // %10 opaklık
            borderWidth: 2.5,
            borderDash: stil.dash,
            pointStyle: stil.nokta,
            pointRadius: 6,
            pointHoverRadius: 9,
            pointBorderWidth: 2,
            pointBorderColor: stil.renk,
            pointBackgroundColor: '#fff',
            tension: 0.35,
            fill: false
        };
    });

    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: { labels: etiketler, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    offset: true // Noktaların eksen sınırlarına yapışmasını önler, aralarını açar
                },
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100,
                    ticks: { callback: v => '%' + v }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
}

// 6. NAVİGASYON FONKSİYONLARI
function sonrakiSoru() {
    if (aktifSoruIndex < 7) { aktifSoruIndex++; grafikOlustur(aktifSoruIndex); }
}

function oncekiSoru() {
    if (aktifSoruIndex > 1) { aktifSoruIndex--; grafikOlustur(aktifSoruIndex); }
}