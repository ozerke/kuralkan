@php
    $translations = [
        'usageType' => __('web.usageType'),
        'insurancePolicyNumber' => __('web.insurancePolicyNumber'),
        'commercial' => __('web.commercial'),
        'private' => __('web.private'),
        'submit' => __('web.submit'),
        'uploadIdDocFront' => __('web.uploadIdDocFront'),
        'uploadIdDocBack' => __('web.uploadIdDocBack'),
        'typeOfTheDocument' => __('web.typeOfTheDocument'),
        'idCard' => __('web.idCard'),
        'newIdCard' => __('web.newIdCard'),
        'temporaryIdDoc' => __('web.temporaryIdDoc'),
        'serial' => __('web.serial'),
        'no' => __('web.no'),
        'serialNo' => __('web.serialNo'),
        'documentNo' => __('web.documentNo'),
        'addressDetails' => __('web.addressDetails'),
        'street' => __('web.street'),
        'neighbourhood' => __('web.neighbourhood'),
        'buildingNo' => __('web.buildingNo'),
        'flatNo' => __('web.flatNo'),
        'documentsYouNeedToAdd' => __('web.documentsYouNeedToAdd'),
        'signatureCircular' => __('web.signatureCircular'),
        'operatingCertificate' => __('web.operatingCertificate'),
        'commercialRegistryGazette' => __('web.commercialRegistryGazette'),
        'officialIdentityFrontSide' => __('web.officialIdentityFrontSide'),
        'officialIdentityBackSide' => __('web.officialIdentityBackSide'),
        'uploadPowerAttorney' => __('web.uploadPowerAttorney'),
        'authorizedName' => __('web.authorizedName'),
        'authorizedNationalId' => __('web.authorizedNationalId'),
        'representationType' => __('web.representationType'),
        'selectRepresentationType' => __('web.selectRepresentationType'),
        'numberOfDocuments' => __('web.numberOfDocuments'),
        'placeOfRetrieval' => __('web.placeOfRetrieval'),
        'documentDate' => __('web.documentDate'),
        'endDate' => __('web.endDate'),
        'unspecified' => __('web.unspecified'),
        'proxy' => __('web.proxy'),
        'official' => __('web.official'),
        'witness' => __('web.witness'),
        'interpreter' => __('web.interpreter'),
        'parent' => __('web.parent'),
        'guardian' => __('web.guardian'),
        'trustee' => __('web.trustee'),
        'representative' => __('web.representative'),
        'legalRepresentative' => __('web.legalRepresentative'),
        'estateRepresentative' => __('web.estateRepresentative'),
        'nationalIdError' => __('web.nationalIdError'),
        'yourNationalId' => __('web.yourNationalId'),
    ];
@endphp
<x-app-layout>
    @section('title')
        {{ __('web.legal-registration-form') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[100px] w-full">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">

            <div class="col-span-1 lg:col-span-7 flex flex-col gap-6">
                <h3 class="font-bold text-2xl">{{ __('web.legal-registration-form') }}</h3>
                <form method="POST"
                    action="{{ route('legal-registration-form.submit', ['orderNo' => $order->order_no]) }}"
                    enctype="multipart/form-data" id="artes-form">
                    @method('POST')
                    @csrf

                    <div class="flex flex-col bg-white gap-4 px-6 py-4">
                        <div id="legal-registration-form" data-translations='@json($translations)'
                            data-artesFields='@json($artesFields)'
                            data-nationalId='{{ $order->invoiceUser->national_id }}'>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-span-1 lg:col-span-3 flex flex-col gap-6">
                <h3 class="font-bold text-2xl">{{ __('web.product') }}</h3>
                <div class="flex flex-col bg-white border-[1px] p-4 gap-2">
                    <div class="flex flex-col lg:flex-row gap-4 py-5">
                        @if ($order->productVariation->firstMedia)
                            <div class="flex justify-center">
                                <img class="max-w-full max-h-[100px] object-contain"
                                    src="{{ $order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                            </div>
                        @endif
                        <div class="flex flex-col justify-center items-center lg:items-start">
                            <p class="font-bold">
                                {{ $order->productVariation->product->currentTranslation->product_name }}</p>
                            <div class="flex justify-center items-center gap-2">
                                @if ($order->productVariation->color->color_image_url)
                                    <img src="{{ $order->productVariation->color->color_image_url }}"
                                        class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                        alt="Color image item" height="40" width="40">
                                @endif
                                <b>{{ $order->productVariation->color->currentTranslation->color_name }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.chasis-no') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $order->chasis_no }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.engine-no') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $order->motor_no }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('js')
        <script>
            $("#artes-form").addEventListener('submit', function(event) {
                if (event.submitter && event.submitter.type !== 'submit') {
                    event.preventDefault();
                }
            });

            $("#artes-form").on('submit', function() {
                $('.trigger-disable').attr('disabled', true);
                if (showLoader) showLoader();
            })

            function submitArtesForm() {
                $("#artes-form").submit();
            }
        </script>
    @endsection
</x-app-layout>
