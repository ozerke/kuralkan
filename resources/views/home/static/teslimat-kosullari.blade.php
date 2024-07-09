<x-app-layout>
    @section('title')
        {!! $homeTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $homeDesc ?? __('web.home-page-description') }}" />
        <meta name="keywords" content="{{ $homeKeywords ?? __('web.home-page-keywords') }}" />
    @endpush
<div class="flex flex-col py-10 px-10 text-gray-900 gap-5">
<h2>{{__('web.delivery-conditions')}}</h2>
                <p>&nbsp;TESLİMAT KOŞULLARI</p>
                <p>&nbsp;</p>
                <p>Siparişleriniz, banka onayı alındıktan sonra 3 iş günü (Pazartesi-Cuma) içerisinde kargoya teslim edilir. Teslimat adresinin ************ ya uzaklığına göre de kargo şirketi 1-3 gün içerisinde siparişinizi size ulaştıracaktır.</p>
                <p>Özel üretim ürünlerin teslim süreleri imalat zamanına göre farklılık göstermektedir. Bu tür ürünlerin teslimat bilgileri ve süreleri ürün sayfalarında belirtilmiştir.</p>
                <p>Tarafımızdan kaynaklanan bir aksilik olması halinde ise size üyelik bilgilerinizden yola çıkılarak haber verilecektir. Bu yüzden üyelik bilgilerinizin eksiksiz ve doğru olması önemlidir. Bayram ve tatil günlerinde teslimat yapılmamaktadır.</p>
                <p>Seçtiğiniz ürünlerin tamamı anlaşmalı olduğumuz&nbsp; kargo şirketleri tarafından ************* garantisi ile size teslim edilecektir.</p>
                <p>Satın aldığınız ürünler bir teyit e-posta'sı ile tarafınıza bildirilecektir. Seçtiğiniz ürünlerden herhangi birinin stokta mevcut olmaması durumunda konu ile ilgili bir e-posta size yollanacak ve ürünün ilk stoklara gireceği tarih tarafınıza bildirilecektir.</p>
                <p>**************** on-line alışveriş sitesidir. Aynı anda birden çok kullanıcıya alışveriş yapma imkanı tanır. Enderde olsa tüketicinin aynı ürünü alması söz konusudur ve ürün stoklarda tükenmektedir bu durumda ;</p>
                <p>Ödemesini internet üzerinden yaptınız ürün eğer stoklarmızda kalmamış ise en az 4 (Dört) en fazla 30 (otuz) gün bekeleme süresi vardır. Ürün bu tarihleri arasında tüketiciye verilemez ise yaptığı ödeme kendisine iade edilir.</p>
</div>
</x-app-layout>