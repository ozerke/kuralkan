<x-app-layout>
@section('title')
        {!! $homeTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $homeDesc ?? __('web.home-page-description') }}" />
        <meta name="keywords" content="{{ $homeKeywords ?? __('web.home-page-keywords') }}" />
    @endpush
<div class="flex flex-col py-10 px-10 text-gray-900 gap-5">
<p>&nbsp;SIKÇA SORULAN SORULAR</p>

<p>&nbsp;</p>

<p><b>Sipariş ettiğim motosiklet nasıl teslim edilecek.</b><br />
Motosiklet sipariş ettiğinizde, call-center ekibimiz sizi arayıp size en yakın servis noktalarımızdan birine yönlendirecek. Motosikletiniz eğer stoktaki bir ürün ise 3 iş günü içersinde servis noktamıza kargoların. Eğer ön sipariş verdiğiniz bir ürün ise stoka girdikten 3 iş günü içersinde call-center ekibi ile belirlediğiniz servise kargolanır. Servis ürünü teslim alıp, sizinle iletişime geçecek. Sizi servis noktamıza davet edecek. Hem ürün hakkında sorularınız cevaplayacak hemde ürünü kontrol etmenizi rica edip ve vb prosesleri tamamlayacak. Ürünü size garanti belgesi, fatura, bakım sıklığı ve vb belge ve bilgiler ile size teslim edecek.<br />
<br />
<b>Yedek parça siparişlerim ne zaman kargolanır.</b><br />
Sipariş ettiğiniz yedek parça ortalama 3 iş günü içersinde kargolanır.<br />
<br />
<b>ekuralkan.com kimdir.</b><br />
Kanuni markasının üreticisi ve sahibi, Bajaj ve supersoco firmasının Türkiye distribütörü firma olan Kuralkan Bilişim Otomotiv‘in online satış sitesidir.<br />
<br />
<b>Yedek parça ve motosiklet iade başvurusunu nasıl yapabilirim.</b><br />
Sitemizin en altına yer alan müşteri iletişim formunu doldurabilirsiniz. Size 72 saat içersinde dönüş yapacaklar.<br />
<br />
<b>Şikayet ve Dilekleri nereye iletebilirim?</b><br />
Sitemizin en altına yer alan müşteri iletişim formunu doldurabilirsiniz. Size 72 saat içersinde dönüş yapacaklar.<br />
<br />
<b>Kargom nerede?</b><br />
Hesabım altındaki siparişlerim sekmesinden siparişiniz güncel durumunu görebilir. Kargo takip numaranızı oradan alabilirsiniz.</p>

<p>&nbsp;</p>


</div>
</x-app-layout>
