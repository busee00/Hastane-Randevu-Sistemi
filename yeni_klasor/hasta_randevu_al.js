$(document).ready(function() {
    
    // 1. Poliklinik seçilince Doktorları getir ve ismini kaydet
    $(document).on('change', '#poliklinik', function() {
        var p_id = $(this).val();
        var p_ad = $("#poliklinik option:selected").text();
        
        // İsmi hemen gizli inputa yaz
        $('#poliklinik_ad').val(p_ad);

        if(p_id) {
            $.post('ajax_islem.php', {islem: 'doktor_getir', pol_id: p_id}, function(data) {
                $('#doktor').html(data);
                $('#doktor_alani').removeClass('d-none');
                $('#takvim_dis_alan, #saat_alani, #tamamla_btn').addClass('d-none');
            });
        } else {
            $('#doktor_alani, #takvim_dis_alan, #saat_alani, #tamamla_btn').addClass('d-none');
        }
    });

    // 2. Doktor seçilince Takvimi göster ve ismini kaydet
    $(document).on('change', '#doktor', function() {
        var d_id = $(this).val();
        var d_ad = $("#doktor option:selected").text();
        
        // İsmi hemen gizli inputa yaz
        $('#doktor_ad').val(d_ad);

        if(d_id) {
            $('#takvim_dis_alan').removeClass('d-none');
        } else {
            $('#takvim_dis_alan, #saat_alani, #tamamla_btn').addClass('d-none');
        }
    });

    // 3. Gün seçilince Saatleri üret
    $(document).on('click', '.gun-btn', function() {
        $('.gun-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
        $(this).addClass('btn-primary active');
        
        var gun = $(this).data('gun');
        var ay = aktifAy; 
        var yil = aktifYil;
        
        // DB formatı: YYYY-MM-DD
        var formatliTarih = yil + "-" + (ay < 10 ? "0"+ay : ay) + "-" + (gun < 10 ? "0"+gun : gun);
                        
        $('#final_tarih').val(formatliTarih);
        var doktorId = $('#doktor').val();

        $.post('ajax_islem.php', {
            islem: 'dolu_saatleri_getir', 
            doktor_id: doktorId, 
            tarih: formatliTarih
        }, function(data) {
            try {
                var doluSaatler = JSON.parse(data);
                saatleriUret(doluSaatler);
                $('#saat_alani').removeClass('d-none');
                $('#tamamla_btn').addClass('d-none'); // Gün değişince butonu gizle
            } catch(e) {
                console.error("Hata:", e);
                saatleriUret([]); 
                $('#saat_alani').removeClass('d-none');
            }
        });
    });

    // 4. Saat Seçme
    $(document).on('click', '.saat-btn', function() {
        if($(this).hasClass('disabled')) return;

        $('.saat-btn').removeClass('btn-info text-white').addClass('btn-outline-info');
        $(this).removeClass('btn-outline-info').addClass('btn-info text-white');
        
        var secilenSaat = $(this).text().split(' ')[0]; 
        $('#secilen_saat').val(secilenSaat);
        
        $('#tamamla_btn').removeClass('d-none');
    });

    // 5. Form Onay ve Eksik Veri Kontrolü
    $('#randevuForm').on('submit', function(e) {
        var pol = $('#poliklinik_ad').val();
        var dok = $('#doktor_ad').val();
        var tar = $('#final_tarih').val();
        var saat = $('#secilen_saat').val();

        // JS tarafında son kontrol (Eksik veri hatasını önlemek için)
        if(!pol || !dok || !tar || !saat || $('#poliklinik').val() == "" || $('#doktor').val() == "") {
            alert("Lütfen tüm alanları seçtiğinizden emin olun!");
            e.preventDefault();
            return false;
        }

        var onay = confirm(
            "Randevuyu Onaylıyor Musunuz?\n\n" +
            "Poliklinik: " + pol + "\n" +
            "Doktor: " + dok + "\n" +
            "Tarih: " + tar + "\n" +
            "Saat: " + saat
        );

        if (!onay) {
            e.preventDefault();
        }
    });

    function saatleriUret(doluSaatler) {
        var html = '';
        for (var h = 9; h <= 16.5; h += 0.5) {
            var saat = Math.floor(h);
            var dk = (h % 1 === 0) ? "00" : "30";
            var tamSaat = (saat < 10 ? "0" + saat : saat) + ":" + dk;

            var isDolu = doluSaatler.includes(tamSaat);
            var disabled = isDolu ? "disabled" : "";
            var btnClass = isDolu ? "btn-danger opacity-25" : "btn-outline-info";
            var metin = isDolu ? tamSaat + " (Dolu)" : tamSaat;

            html += `<button type="button" class="btn ${btnClass} m-1 saat-btn" ${disabled}>${metin}</button>`;
        }
        $('#saat_listesi').html(html);
    }
});