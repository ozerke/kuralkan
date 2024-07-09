<style>
    body {
        font-family: DejaVu Sans;
    }

    #remote-agreement-doc h1,
    #remote-agreement-doc h2,
    #remote-agreement-doc h3 {
        margin-bottom: 10px;
        margin-top: 10px;
        font-weight: 600;
    }

    #remote-agreement-doc table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    #remote-agreement-doc th,
    #remote-agreement-doc td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
</style>
<div id="remote-agreement-doc">
    <h2>MADDE 1- TARAFLAR</h2>

    <h3>1.1- SATICI:</h3>
    <p>Ünvanı: Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</p>
    <p>Adresi: Tepeören, Eski Ankara Asfaltı Cd. No:206, 34940 Tuzla/İstanbul. Kuralkan Bilişim Otomotiv</p>
    <p>Telefon: 08502096565</p>
    <p>Fax: -</p>
    <p>E-mail: info@ekuralkan.com</p>

    <h3>1.2- ALICI:</h3>
    <p>Adı/Soyadı/Ünvanı: {{ $tosFullname }}</p>
    <p>Adresi: {{ $tosAddress }}</p>
    <p>Telefon: {{ $tosPhone }}</p>
    <p>E-mail: {{ $tosEmail }}</p>

    <h2>MADDE 2- KONU</h2>

    <p>İşbu sözleşmenin konusu, ALICI'nın SATICI'ya ait Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş. internet
        sitesinden elektronik ortamda siparişini yaptığı aşağıda nitelikleri ve satış fiyatı belirtilen ürünün satışı ve
        teslimi ile ilgili olarak 4077 sayılı Tüketicilerin Korunması Hakkındaki Kanun ve Mesafeli Sözleşmeleri Uygulama
        Esas ve Usulleri Hakkında Yönetmelik hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>

    <h2>MADDE 3- SÖZLEŞME KONUSU ÜRÜN</h2>
    <p>3.1. Malın / Ürün/Ürünlerin / Hizmetin temel özellikleri (türü, miktarı, marka/modeli, rengi, adedi) SATICI’ya
        ait internet sitesinde yer almaktadır. Ürünün temel özelliklerini internet sitesinden inceleyebilirsiniz.</p>
    <p>3.2. Listelenen ve sitede ilan edilen fiyatlar satış fiyatıdır. İlan edilen fiyatlar ve vaatler güncelleme
        yapılana ve değiştirilene kadar geçerlidir. Süreli olarak ilan edilen fiyatlar ise belirtilen süre sonuna kadar
        geçerlidir.</p>
    <p>3.3. Sözleşme konusu mal ya da hizmetin Kdv ve Ötv dahil MTV hariç satış fiyatı aşağıdaki tabloda gösterilmiştir.
        Hesaplanan vergiler geçici bilgi niteliğinde olup; vergiler fatura kesildiği anda ki vergi oranları üzerinden
        tekrardan hesaplanıp alıcıya fatura edilecek ve işbu güncel tutar ürünün satış fiyatı olacaktır. Mtv müşteriye
        aittir. Ayrıca faturanın kesildiği ana kadar İDARE’nin tasarrufları ile getirilebilecek diğer tüm vergiler ve
        mevcut vergilerdeki artışlar müşteriye yansıtılacaktır.
    </p>
    <br />
    <p style="font-weight: bold;">Alıcı işbu mesafeli satış sözleşmesini onayladıktan sonra, aynı gün mesai bitimine
        kadar sözleşme konusu
        siparişin toplam tutarını eksiksiz olarak ödemekle yükümlüdür. Mesai saatleri 08.00-18.00 arasıdır. Eğer müşteri
        ödemesini aynı gün saat 18.00’a kadar tamamlamaz ise Satıcı işbu siparişi iptal etme hakkına sahip olup, Alıcı
        iptal halinde herhangi bir talepte bulunamayacağını kabul ve taahhüt etmiştir.</p>
    <br />
    <p>Tarih: {{ $dateTime }}</p>

    <table border="1">
        <tr>
            <th>Ürün Adı</th>
            <th>Adet</th>
            <th>Toplam Ürün Tutarı</th>
        </tr>
        <tr>
            <td>{{ $tosProductName }}</td>
            <td>1</td>
            <td>{{ $tosPrice }}</td>
        </tr>
    </table>

    <p>Ürünlerin cinsi ve türü, miktarı, marka/modeli rengi satış bedeli yukarıda belirtildiği gibidir.</p>
    <p>Ödeme şekli:</p>
    <p>Teslimat adresi: {{ $tosDeliveryAddress }}</p>
    <p>ile Toplam {{ $tosPrice }}</p>

    <h2>MADDE 4- GENEL HÜKÜMLER</h2>
    <p>4.1- ALICI, Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş. internet sitesinde sözleşme konusu ürünün temel
        nitelikleri, satış fiyatı ve ödeme şekli ile teslimata ilişkin ön bilgileri okuyup bilgi sahibi olduğunu ve
        elektronik ortamda gerekli teyidi verdiğini beyan eder.</p>
    <p>4.2- Sözleşme konusu ürün, yasal 3 günlük süreyi aşmamak koşulu ile her bir ürün için ALICI'nın yerleşim yerinin
        uzaklığına bağlı olarak internet sitesinde ön bilgiler içinde açıklanan süre içinde ALICI veya gösterdiği
        adresteki kişi/kuruluşa teslim edilir.</p>
    <p>4.3- Sözleşme konusu ürün, ALICI'dan başka bir kişi/kuruluşa teslim edilecek ise, teslim edilecek kişi/kuruluşun
        teslimatı kabul etmemesininden SATICI sorumlu tutulamaz.</p>
    <p>4.4- SATICI, sözleşme konusu ürünün sağlam, eksiksiz, siparişte belirtilen niteliklere uygun ve varsa garanti
        belgeleri ve kullanım klavuzları ile teslim edilmesinden sorumludur.</p>
    <p>4.5- Sözleşme konusu ürünün teslimatı için işbu sözleşmenin imzalı nüshasının SATICI'ya ulaştırılmış olması ve
        bedelinin ALICI'nın tercih ettiği ödeme şekli ile ödenmiş olması şarttır. Herhangi bir nedenle ürün bedeli
        ödenmez veya banka kayıtlarında iptal edilir ise, SATICI ürünün teslimi yükümlülüğünden kurtulmuş kabul edilir.
    </p>
    <p>4.6- Ürünün tesliminden sonra ALICI'ya ait kredi kartının ALICI'nın kusurundan kaynaklanmayan bir şekilde
        yetkisiz kişilerce haksız veya hukuka aykırı olarak kullanılması nedeni ile ilgili banka veya finans kuruluşun
        ürün bedelini SATICI'ya ödememesi halinde, ALICI'nın kendisine teslim edilmiş olması kaydıyla ürünün 3 gün
        içinde SATICI'ya gönderilmesi zorunludur. Bu takdirde nakliye giderleri ALICI'ya aittir.</p>
    <p>4.7- SATICI mücbir sebepler veya nakliyeyi engelleyen hava muhalefeti, ulaşımın kesilmesi gibi olağanüstü
        durumlar nedeni ile sözleşme konusu ürünü süresi içinde teslim edemez ise, durumu ALICI'ya bildirmekle
        yükümlüdür. Bu takdirde ALICI siparişin iptal edilmesini, sözleşme konusu ürünün varsa emsali ile
        değiştirilmesini, ve/veya teslimat süresinin engelleyici durumun ortadan kalkmasına kadar ertelenmesi
        haklarından birini kullanabilir. ALICI'nın siparişi iptal etmesi halinde ödediği tutar 10 gün içinde kendisine
        nakten ve defaten ödenir.</p>
    <p>4.8- Garanti belgesi ile satılan ürünlerden olan veya olmayan ürünlerin arızalı veya bozuk olanlar, garanti
        şartları içinde gerekli onarımın yapılması için SATICI'ya gönderilebilir, bu takdirde kargo giderleri SATICI
        tarafından karşılanacaktır.</p>

    <h2>MADDE 5- CAYMA HAKKI VE CAYMA HAKKI KULANILAMAYACAK ÜRÜNLER</h2>

    <p>ALICI; mal satışına ilişkin mesafeli sözleşmelerde, ürünün kendisine veya gösterdiği adresteki kişi/kuruluşa
        teslim tarihinden Mesafeli Sözleşmeler Yönetmeliği 15/I maddesi uyarınca 3/10/1983 tarihli ve 2918 sayılı
        Karayolları Trafik Kanununa göre tescili zorunlu olan taşınırlar ile kayıt veya tescil zorunluluğu bulunan
        insansız hava araçlarına ilişkin sözleşmeler cayma hakkı kapsamı dışında olup, işbu hüküm uyarınca sözleşme
        konusu motosiklet için cayma hakkı kullanılamaz. ALICI, iş bu sözleşmeyi kabul etmekle, cayma hakkı konusunda
        bilgilendirildiğini peşinen kabul eder.</p>

    <h2>MADDE 6- CAYMA PARASI</h2>

    <p>Sözleşme online ortamda kurulduktan sonra ve ürün fatura edilmeden önce ALICI’nın sözleşmeden cayması halinde
        SATICI’ya sözleşme konusu ürünün toplam bedelinin %2’si oranında cayma parası ödeyeceği taraflarca kabul edilmiş
        olup, işbu bedel peşinat ödemesinden düşülecektir. Peşinat ödemesi yoksa veyahut yeterli gelmez ise ALICI
        SATICI’nın ilk talebi halinde herhangi bir dava, icra vs. gerek olmaksızın işbu bedeli nakden ve derhal
        ödeyeceğini kabul, beyan ve taahhüt eder.</p>

    <h2>MADDE 7- YETKİLİ MAHKEME</h2>

    <p>İşbu sözleşmenin uygulanmasında, Sanayi ve Ticaret Bakanlığınca ilan edilen değere kadar Tüketici Hakem Heyetleri
        ile ALICI'nın veya SATICI'nın yerleşim yerindeki Tüketici Mahkemeleri yetkilidir.</p>

    <p>Siparişin gerçekleşmesi durumunda ALICI işbu sözleşmenin tüm koşullarını kabul etmiş sayılır.</p>

    <p>SATICI</p>
    <p>Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</p>

    <p>ALICI</p>
    <p>{{ $tosFullname }}</p>
</div>
