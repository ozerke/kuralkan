<x-app-layout>
    @section('title')
        {!! $homeTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $homeDesc ?? __('web.home-page-description') }}" />
        <meta name="keywords" content="{{ $homeKeywords ?? __('web.home-page-keywords') }}" />
    @endpush
    <div class="flex flex-col py-10 px-10 text-gray-900 gap-5">

        <h1>Banka Hesap Bilgilerimiz</h1>

        <div class="flex flex-col">

            <div class="flex flex-col gap-4">
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/kuveyt.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Kuveyt Türk
                                - TRY -
                                İstanbul Anadolu Kurumsal Şubesi -
                                Şube kodu:
                                238</span>
                            <span class="text-sm">Hesap numarası:
                                57226-12</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR97 0020 5000 0000 5722 6000 12</span>
                                <button onclick="copyToClipboard('TR97 0020 5000 0000 5722 6000 12', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/turkiyefinans.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Türkiye Finans
                                - TRY -
                                BOĞAZİÇİ KURUMSAL ŞUBESİ -
                                Şube kodu:
                                289</span>
                            <span class="text-sm">Hesap numarası:
                                30020-15</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR46 0020 6001 9200 0300 2000 15</span>
                                <button onclick="copyToClipboard('TR46 0020 6001 9200 0300 2000 15', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/akbank.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Akbank
                                - TRY -
                                İMES TİCARİ -
                                Şube kodu:
                                0876</span>
                            <span class="text-sm">Hesap numarası:
                                77021</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR37 0004 6008 7688 8000 0770 21</span>
                                <button onclick="copyToClipboard('TR37 0004 6008 7688 8000 0770 21', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/is_bankasi.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">T. İş Bankası
                                - TRY -
                                DUDULLU TİCARİ -
                                Şube kodu:
                                1381</span>
                            <span class="text-sm">Hesap numarası:
                                19726</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR82 0006 4000 0011 3810 0197 26</span>
                                <button onclick="copyToClipboard('TR82 0006 4000 0011 3810 0197 26', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/halk_bankasi.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">T. Halk Bankası
                                - TRY -
                                İMES TİCARİ -
                                Şube kodu:
                                615</span>
                            <span class="text-sm">Hesap numarası:
                                10100303</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR30 0001 2009 6150 0010 1003 03</span>
                                <button onclick="copyToClipboard('TR30 0001 2009 6150 0010 1003 03', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/ziraat.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Ziraat Bankası
                                - TRY -
                                TUZLA OSB GİRİŞİMCİ ŞUBESİ -
                                Şube kodu:
                                2283</span>
                            <span class="text-sm">Hesap numarası:
                                2901076-5011</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR18 0001 0022 8302 9010 7650 11</span>
                                <button onclick="copyToClipboard('TR18 0001 0022 8302 9010 7650 11', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/finansbank.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Finansbank
                                - TRY -
                                ANADOLU TİCARİ MERKEZ -
                                Şube kodu:
                                00875</span>
                            <span class="text-sm">Hesap numarası:
                                48891675</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR45 0011 1000 0000 0048 8916 75</span>
                                <button onclick="copyToClipboard('TR45 0011 1000 0000 0048 8916 75', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/vakif.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Vakıf Bank
                                - TRY -
                                IMES DUDULLU TİCARİ ŞUBESİ -
                                Şube kodu:
                                1244</span>
                            <span class="text-sm">Hesap numarası:
                                158007292821038</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR72 0001 5001 5800 7292 8210 38</span>
                                <button onclick="copyToClipboard('TR72 0001 5001 5800 7292 8210 38', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/garanti.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Garanti
                                - TRY -
                                İMES TİCARİ -
                                Şube kodu:
                                1616</span>
                            <span class="text-sm">Hesap numarası:
                                6296242</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                    TR40 0006 2001 6160 0006 2962 42</span>
                                <button onclick="copyToClipboard('TR40 0006 2001 6160 0006 2962 42', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                    <div
                        class="hidden border-red-300 border-yellow-300 border-pink-300 border-purple-300 border-cyan-300 border-orange-300 border-green-300 border-black border-blue-300">
                    </div>

                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                        <div class="flex lg:justify-center items-center">
                            <img src="https://ekuralkan.com/build/images/banks/main/yapikredi.png"
                                class="h-[40px] w-[100px] object-contain">
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-md font-bold">Kuralkan Bilişim Otomotiv San. ve Dış Tic. A.Ş.</span>
                            <span class="text-sm font-bold">Yapı Kredi
                                - TRY -
                                GEBZE ORGANİZE SANAYİ TİCARİ ŞUBESİ -
                                Şube kodu:
                                1351</span>
                            <span class="text-sm">Hesap numarası:
                            71410981</span>
                            <div class="flex gap-2">
                                <span class="text-sm">IBAN:
                                TR88 0006 7010 0000 0071 4109 81</span>
                                <button onclick="copyToClipboard('TR88 0006 7010 0000 0071 4109 81', 'Kopyala')"
                                    type="button" class="copy-button"><img
                                        src="https://ekuralkan.com/build/images/icons/copy.png"
                                        class="h-[18px] w-auto"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @section('js')
        <script>
            async function copyToClipboard(value, message) {
                await navigator.clipboard.writeText(value);
                alert(message);
            }
        </script>
    @endsection
</x-app-layout>
