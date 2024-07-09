<footer class="bg-white flex flex-col">
    <div class="bg-gray-400 footer-newsletter flex justify-center">
        <div class="relative w-full max-w-[700px] text-[#161616] px-[30px]">
            <div class="big-text">{{ __('web.email-newsletter-sign-up') }}</div>
            <div class="small-text">{{ __('web.for-news-and-special-offers') }}</div>
            <div class="newsletter-form">
                <label>{{ __('web.email-newsletter-sign-up') }}</label>
                <div class="block mt-[30px] mb-[20px] relative">
                    <form method="POST" action="{{ route('submit-newsletter') }}">
                        @csrf
                        @method('POST')
                        <input type="text" class="newstext textbox" name="txtbxNewsletterMail"
                            id="txtbxNewsletterMail" placeholder="{{ __('web.email-newsletter-placeholder') }}">
                        <button type="submit" id="btnMailKaydet" href="javascript:void(0)"
                            class="newsbutton button">Gönder</button>
                    </form>
                </div>
            </div>
            <span>{{ __('web.email-newsletter-disclaimer') }}</span>
        </div>
    </div>
    <div class="mx-auto w-full space-y-8 px-[30px] pt-16 pb-8 sm:px-6 lg:space-y-16 lg:px-[60px]">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:col-span-2 lg:grid-cols-3">
                <div>
                    <p class="footer-link-heading">{{ __('web.important-information') }}</p>

                    <ul>
                        <li>
                            <a href="/iletisim" class="footer-link">{{ __('web.bank-account-numbers') }}</a>
                        </li>
                        <li>
                            <a href="/teslimat-kosullari" class="footer-link">{{ __('web.delivery-conditions') }}</a>
                        </li>
                        <li>
                            <a href="/garanti-ve-iade-kosullari"
                                class="footer-link">{{ __('web.warranty-and-return-conditions') }}</a>
                        </li>
                        <li>
                            <a href="/gizlilik-ve-guvenlik"
                                class="footer-link">{{ __('web.privacy-and-security') }}</a>
                        </li>
                        <li>
                            <a href="/hakkimizda" class="footer-link">{{ __('web.about-us') }}</a>
                        </li>
                        <li>
                            <a href="/uyelik-sozlesmesi" class="footer-link">{{ __('web.membership-agreement') }}</a>
                        </li>
                        <li>
                            <a href="/sikca-sorulan-sorular" class="footer-link">{{ __('web.faq') }}</a>
                        </li>
                        <li>
                            <a href="#" class="footer-link">{{ __('web.application-for-dealers') }}</a>
                        </li>

                    </ul>
                </div>

                <div>
                    <p class="footer-link-heading">{{ __('web.fast-access') }}</p>

                    <ul>
                        <li>
                            <a href="/" class="footer-link">{{ __('web.home-page') }}</a>
                        </li>
                        <li>
                            <a href="/login" class="footer-link">{{ __('web.my-orders') }}</a>
                        </li>
                        <li>
                            <a href="/sepetim" class="footer-link">{{ __('web.my-cart') }}</a>
                        </li>
                        {{-- 
                        <li>
                            <a href="#" class="footer-link">{{ __('web.note-sales-page') }}</a>
                        </li>
                        --}}
                        <li>
                            <a href="/motosiklet-satis-noktalari" class="footer-link">{{ __('web.motorcycle-shops-sales-points') }}</a>
                        </li>
                        <li>
                            <a href="/motosiklet-teslimat-noktalari" class="footer-link">{{ __('web.motorcycle-service-points') }}</a>
                        </li>
                        <li>
                            <a href="https://blog.ekuralkan.com/" class="footer-link">{{ __('web.blog') }}</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="footer-link-heading">{{ __('web.categories') }}</p>

                    <ul>
                    <li>
                            <a href="/bajaj" class="footer-link">Bajaj</a>
                        </li>
                        <li>
                            <a href="/kanuni" class="footer-link">Kanuni</a>
                        </li>
                        {{-- 
                        <li>
                            <a href="#" class="footer-link">{{ __('web.principles') }}</a>
                        </li>
                        --}}
                        <li>
                            <a href="https://yedekparca.ekuralkan.com/" class="footer-link">{{ __('web.spare-part') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div>
                <a href="/">
                    <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}" alt="">
                </a>

                <div class="flex flex-col mt-[40px] gap-[20px]">
                    <div class="flex flex-col">
                        <div class="flex">
                            <img src="{{ URL::asset('build/images/menu/location.png') }}" alt="Address"
                                class="h-[25px] w-auto mr-[10px]">
                            <span>{{ __('web.address') }}</span>
                        </div>
                        <div class="text-[13px] font-[400] ml-[35px]">Tepeören Mah. Eski Ankara Asfaltı No:206 34940
                            Tuzla / istanbul</div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <img src="{{ URL::asset('build/images/menu/phone.png') }}" alt="Address"
                                class="h-[25px] w-auto mr-[10px]">
                            <span>{{ __('web.phone') }}</span>
                        </div>
                        <div class="text-[22px] font-[600] ml-[35px] leading-[50px] text-[#0e60ae]"><a
                                href="tel:08502096565">0850 209 6565</a></div>
                        <div class="text-[13px] font-[400] ml-[35px]">{{ __('web.mid-week') }}: 08:00 - 18:00</div>

                    </div>
                </div>

                <ul class="mt-8 flex gap-6">
                    <li>
                        <a href="/" rel="noreferrer" target="_blank"
                            class="text-gray-700 transition hover:opacity-75">
                            <span class="sr-only">Facebook</span>

                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>

                    <li>
                        <a href="/" rel="noreferrer" target="_blank"
                            class="text-gray-700 transition hover:opacity-75">
                            <span class="sr-only">Instagram</span>

                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>

                    <li>
                        <a href="/" rel="noreferrer" target="_blank"
                            class="text-gray-700 transition hover:opacity-75">
                            <span class="sr-only">Twitter</span>

                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </li>

                    <li>
                        <a href="/" rel="noreferrer" target="_blank"
                            class="text-gray-700 transition hover:opacity-75">
                            <span class="sr-only">WhatsApp</span>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                        </a>
                    </li>

                    <li>
                        <a href="/" rel="noreferrer" target="_blank"
                            class="text-gray-700 transition hover:opacity-75">
                            <span class="sr-only">Youtube</span>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                            </svg>
                        </a>
                    </li>
                </ul>
                <div class="mt-[30px] contact-form-button rounded-md">
                    <a href="/musteri-iletisim-formu">{{ __('web.customer-contact-form') }}</a>
                </div>

                <div class="grid-cols-1" style="margin: 90px auto 0; text-align:center;">
                    <div id="ETBIS" style="margin-top:10px;">
                    <div id="4674776827753420">
                    <a href="https://etbis.eticaret.gov.tr/sitedogrulama/4674776827753420" target="_blank">
                    <img style="width:100px; height:120px; margin:10px auto;" 
                    src="data:image/jpeg;base64, iVBORw0KGgoAAAANSUhEUgAAAIIAAACWCAYAAAASRFBwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAEU3SURBVHhe7Z0HnF5F9f4lQUIgJBQB6SBIB4UEQkcEBREEKSK9BUFBpER67ygKSgcBITSlG5oRKYG0zdbspkAC6b33CvM/37vvc3Peu/OW3SxBf3+fz+eB7LR733vPzJxz5szcrxlCa/Avf/lLALNmzQrrrbdek/xevXol+R433XRTk3IHHHBALjeEc845p0m+59tvv52U+/TTT0Pbtm2TtCuvvDJJA7vsskuTOuJaa60VJk+enJR7/vnn0/Q33ngjSfO444470vza2tpc6nKcf/75Sd6aa64ZJk6cmEttilGjRoWvf/3rSdnf/va3udTimD9/fth4442TOscdd1wuNYSf/OQn6T2tIF8yJohlNpulBCH2gGOC0KVLl1xuCL/+9a+b5HtKED755JM07YorrkjSwFZbbZVXPsvFixcn5bg3pcXu889//nOaHxOE8847L80fP358LrUpZs+enQrCddddl0stjU033TSps1IEYeuttw6nnnpqs/iDH/wgbTAmCN/61rfSsvX19Uk+D+Opp55KeMMNN+S1By+88MI0/3vf+17afowSBHr26aefntTnAav+L3/5yybti2eeeWZyz5RTj4YShLlz54YePXok+T/72c/SfAnClClT0utcddVVSZvdunVLfj+g9ytf/OMf/5iOXIcffniT/Bi5x3XWWSepExMERqGTTz65ye8rxpNOOimsscYa+k35gvCLX/wid4nyUVlZqcaigsCDyWLo0KFpnWuvvTaXuhwVFRVpfilKEDxuv/32NH/kyJG51DjatWuX1x588803k7wRI0Y0yYMShA8++CBNe+ihh5I0j6effjqvXmswJghbbrllLqV52GyzzdRuviDQo5qLd999V41FBeGss85K0jwYxldZZZUk/5ZbbsmlLodvsxRjgnDbbbel+egOxbDhhhvmtQffe++9JI8ev+qqqzbJr6qqSvL79OmTpum3e/Ts2TOvXmswJgibb755OsWVi4ULF4ZNNtlE7RYWhMsvvzwceuihBTlmzJikXClBQMlRHT1ALwhMR8pnKAUzZsxIXnAhcm+6Jn9n4QVhn332SdvP8uCD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"/>
                    
                    </a>
                    </div>
                </div>

                </div>

            </div>
        </div>
    </div>
    <div class="w-full border-t-[1px] border-[#ecedee]">
        <img class="w-full p-[20px] image-desktop" src="{{ URL::asset('build/images/menu/footer-providers.png') }}"
            alt="Bank Providers">
        <img class="w-full p-[20px] image-mobile"
            src="{{ URL::asset('build/images/menu/footer-providers-mobile.png') }}" alt="Bank Providers">
    </div>
    <div class="w-full flex bg-[#0e60ae] py-[15px] px-[60px]">
        <div class="flex items-center justify-between w-full flex-col lg:flex-row gap-[12px] lg:gap-0">
            <div class="text-white text-[12px] leading-[34px] text-center lg:text-left">
                Copyright© {{ now()->year }} <b>EKURALKAN</b> All rights reserved. | ekuralkan.com bir Kuralkan
                Bilişim Otomotiv San. ve Dış Tic. A.Ş. markasıdır.
            </div>
        </div>
    </div>
</footer>
