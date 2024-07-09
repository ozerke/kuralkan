@php
    $paymentInfo = $order->getOrderPaymentsState(true);
    $notaryStatus = $order->salesAgreement ? $order->salesAgreement->getNotaryDocumentStatus() : null;
    $bondPayments = $order->getBondsPayments();
@endphp
<x-app-layout>
    @section('title')
        {{ __('web.order-details') }}
    @endsection
    <div class="flex flex-col py-10 px-10 bg-gray-200 gap-5">
        <div class="flex w-full items-center justify-start">
            <a href="{{ route('panel') }}"
                class="flex justify-center items-center bg-blue-500 p-2 rounded-md text-white">{{ __('web.go-back') }}</a>
        </div>
        <div class="w-full flex flex-col lg:flex-row bg-white p-5 justify-between items-center gap-5">
            <div class="flex flex-row lg:flex-col gap-2">
                <div class="text-gray-500">{{ __('web.order-number') }}</div>
                <div class="font-bold">{{ $order->order_no }}</div>
            </div>
            <div class="flex flex-row gap-5 items-center">
                <div class="font-bold">{{ __('app.order-date') }}: {{ $order->created_at->format('d-m-Y H:i') }}</div>
                <div class="flex justify-center items-center bg-blue-500 p-2 rounded-md">
                    <span
                        class="font-bold text-white">{{ optional($order->latestStatusHistory)->orderStatus->currentTranslation->status ?? __('web.pending') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
            <div class="bg-white flex flex-col gap-3 p-4">
                <div class="border-b-2 border-black">
                    <span class="text-lg font-bold">{{ __('web.product-information') }}</span>
                </div>
                <div class="w-full h-auto">
                    <img
                        src="{{ $order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                </div>
                <div class="font-bold">
                    {{ $order->productVariation->product->currentTranslation->product_name }} -
                    {{ $order->productVariation->color->currentTranslation->color_name }}
                </div>
                <div>
                    {{ __('web.stock-code') }}: {{ $order->productVariation->product->stock_code }}
                </div>
                @if ($order->motor_no || $order->chasis_no)
                    <div class="bg-blue-500 rounded-md flex flex-col gap-4 justify-around py-2 px-4">
                        @if ($order->motor_no)
                            <span class="font-bold text-white">{{ __('web.engine-no') }}:
                                {{ $order->motor_no }}</span>
                        @endif
                        @if ($order->chasis_no)
                            <span class="font-bold text-white">{{ __('web.chassis-no') }}:
                                {{ $order->chasis_no }}</span>
                        @endif
                    </div>
                @endif

                @if ($order->temprorary_licence_doc_link)
                    <a href="{{ $order->temprorary_licence_doc_link }}" target="_blank"
                        class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-download"></i> {{ __('web.temporary-plate') }}
                    </a>
                @else
                    <a class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                        <i class="fa-solid fa-download"></i> {{ __('web.temporary-plate') }}
                    </a>
                @endif

                @if ($order->plate_printing_doc_link)
                    <a href="{{ $order->plate_printing_doc_link }}" target="_blank"
                        class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-download"></i> {{ __('web.temporary-license') }}
                    </a>
                @else
                    <a class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                        <i class="fa-solid fa-download"></i> {{ __('web.temporary-license') }}
                    </a>
                @endif
            </div>
            <div class="bg-white flex flex-col gap-3 p-4">
                @if ($order->isOrderedByShop())
                    <div class="border-b-2 border-black">
                        <span class="text-lg font-bold">{{ __('web.sales-point-information') }}</span>
                    </div>
                    <p>{{ __('web.shop-name') }}: {{ $order->user->site_user_name }}</p>
                    <p>{{ __('web.district-city') }}:
                        {{ $order->user->district->currentTranslation->district_name }}
                        @if ($order->user->getCity())
                            / {{ $order->user->getCity()->currentTranslation->city_name }}
                        @endif
                    </p>
                    <p>{{ __('web.phone') }}: {{ $order->user->phone }}</p>
                @endif
                <div class="border-b-2 border-black">
                    <span class="text-lg font-bold">{{ __('web.invoice-information') }}</span>
                </div>
                <p>
                    {{ __('web.name-company') }}: {{ $order->invoiceUser->getInvoiceName() }}
                </p>
                <p>
                    {{ __('web.address') }}: {{ $order->invoiceUser->address }}
                </p>
                <p>
                    {{ __('web.district-city') }}:
                    {{ $order->invoiceUser->district->currentTranslation->district_name }}
                    @if ($order->invoiceUser->getCity())
                        / {{ $order->invoiceUser->getCity()->currentTranslation->city_name }}
                    @endif
                </p>
                <p>
                    {{ __('web.tc-no-tax-no') }}: {{ $order->invoiceUser->getTaxOrNationalId() }}
                </p>
                @if ($order->invoiceUser->isCompany())
                    <p>
                        {{ __('web.tax-info') }}: {{ $order->invoiceUser->tax_office }}
                    </p>
                @endif

                @if ($order->invoice_link)
                    <a href="{{ $order->invoice_link }}" target="_blank"
                        class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                        <i class="fa-solid fa-download"></i> {{ __('web.invoice') }}
                    </a>
                @else
                    <a class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                        <i class="fa-solid fa-download"></i> {{ __('web.invoice') }}
                    </a>
                @endif

                @if (!$order->approvedLegalForm())
                    @if (empty($order->temprorary_licence_doc_link))
                        @if ($order->showLegalRegistrationForm())
                            <a href="{{ route('legal-registration-form', ['orderNo' => $order->order_no]) }}"
                                class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                                <i class="fa-solid fa-file-lines"></i> {{ __('web.legal-registration-form') }}
                            </a>
                        @else
                            @if ($order->pendingLegalForm())
                                <a class="text-white font-bold bg-gray-500 rounded-md py-2 px-4 cursor-not-allowed">
                                    <i class="fa-solid fa-file-lines"></i> {{ __('web.legal-registration-form') }}
                                </a>
                            @else
                                <a class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                                    <i class="fa-solid fa-file-lines"></i> {{ __('web.legal-registration-form') }}
                                </a>
                            @endif
                        @endif
                    @endif
                @endif
                @if (
                    !$order->approvedLegalForm() &&
                        !$order->pendingLegalForm() &&
                        $artesResponse &&
                        !empty($artesResponse['is_registered']) &&
                        $artesResponse['is_registered'] == '40')
                    <div class="rounded-md bg-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-md font-medium text-red-700">
                                    {{ __('web.artes-message') }}
                                </h3>

                            </div>
                        </div>
                    </div>
                @endif
                <div class="border-b-2 border-black">
                    <span class="text-lg font-bold">{{ __('web.delivery-information') }}</span>
                </div>
                <p>{{ __('web.service-name') }}: {{ $order->deliveryUser->site_user_name }}</p>
                <p>{{ __('web.address') }}: {{ $order->deliveryUser->address }}</p>
                <p>{{ __('web.district-city') }}:
                    {{ $order->deliveryUser->district->currentTranslation->district_name }}
                    @if ($order->deliveryUser->getCity())
                        / {{ $order->deliveryUser->getCity()->currentTranslation->city_name }}
                    @endif
                </p>
                <p>{{ __('web.phone') }}: {{ $order->deliveryUser->phone }}</p>
                <p>{{ __('web.email') }}: {{ $order->deliveryUser->erp_email }}</p>
                @if ($order->delivery_date)
                    <p class="font-bold bg-blue-500 text-white rounded-md py-2 px-4">{{ __('web.delivery-date') }}:
                        {{ $order->getDeliveryDate() }}
                    </p>
                @endif
            </div>
            <div class="bg-white flex flex-col gap-3 p-4">
                <div class="border-b-2 border-black">
                    <span class="text-lg font-bold">{{ __('web.payment-information') }}</span>
                </div>
                @if ($order->isSalesAgreementOrder())
                    <p>
                        <span>{{ __('web.application-status') }}:</span>
                        <span
                            class="@if ($order->sa_status['color']) {{ $order->sa_status['color'] }} @endif text-white font-bold p-1 rounded-md text-nowrap">{{ $order->sa_status['text'] }}
                            @if ($order->salesAgreement && $order->salesAgreement->is_sms_pending)
                                ({{ __('web.sms-pending') }})
                            @endif
                        </span>
                    </p>
                @endif
                <p>{{ __('web.payment-type') }}: {{ $order->getOrderPaymentType(true) }}</p>
                @if ($order->payment_type === 'S')
                    <p>{{ __('web.down-payment') }}: {{ $order->salesAgreement->down_payment_amount ?? '-' }}
                        TL
                    </p>
                    <p>{{ __('web.payment-left') }}:
                        {{ number_format($paymentInfo['remaining_amount'], 2, ',', '.') }} TL
                    </p>
                    <p>{{ __('web.installment-amount') }}:
                        {{ $order->salesAgreement->monthly_payment ?? '-' }}
                        TL/{{ __('web.month') }}
                    </p>
                    <p>{{ __('web.installments') }}:
                        {{ $order->salesAgreement->number_of_installments ?? '-' }}
                    </p>
                    @if (!$paymentInfo['is_paid'] && !$order->isCancelled())
                        <a href="{{ route('redirect-to-payment', ['orderNo' => $order->order_no]) }}"
                            class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                            <i class="fa-solid fa-money-bill"></i> {{ __('web.pay-now') }}
                        </a>
                    @endif
                    @if (!empty($order->salesAgreement->agreement_document_link))
                        <a href="{{ $order->salesAgreement->agreement_document_link }}" target="_blank"
                            class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                            <i class="fa-solid fa-download"></i> {{ __('web.sales-agreement') }}
                        </a>
                    @else
                        <a class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                            <i class="fa-solid fa-download"></i> {{ __('web.sales-agreement') }}
                        </a>
                    @endif
                @else
                    <p>{{ __('web.order-amount') }}: {{ number_format($order->total_amount, 2, ',', '.') }} TL</p>
                    <p>{{ __('web.payment-amount') }}: {{ number_format($paymentInfo['paid_amount'], 2, ',', '.') }}
                        TL
                    </p>
                    <p>{{ __('web.payment-left') }}:
                        {{ number_format($paymentInfo['remaining_amount'], 2, ',', '.') }} TL
                    </p>
                    @if (!$paymentInfo['is_paid'] && !$order->isCancelled())
                        <a href="{{ route('redirect-to-payment', ['orderNo' => $order->order_no]) }}"
                            class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                            <i class="fa-solid fa-money-bill"></i> {{ __('web.pay-now') }}
                        </a>
                    @endif
                @endif

                <div class="h-2 bg-black w-full rounded-md"></div>

                @if ($notaryStatus && !$order->isCancelled() && $order->latest_order_status_id < 2)
                    @if ($notaryStatus['notary_front']['rejected'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'notary_front']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert type="error" shade="faint" show_close_icon="false"
                                    class="py-4 mb-4">
                                    <p class="mb-4 font-bold">{{ __('web.notary-document-front-rejected') }}</p>
                                    <p>{{ __('web.reason') }}: {{ $notaryStatus['rejection_reason'] }}</p>
                                </x-bladewind.alert>
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="notary_document_front" required
                                                placeholder="{{ __('web.upload-notary-document-front') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif(!$notaryStatus['notary_front']['is_uploaded'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'notary_front']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="notary_document_front" required
                                                placeholder="{{ __('web.upload-notary-document-front') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif($notaryStatus['notary_front']['is_uploaded'] && !$notaryStatus['notary_front']['rejected'])
                        <div class="flex flex-col my-4">
                            <x-bladewind.alert shade="faint" show_close_icon="false" class="py-4">
                                <p class="mb-4 font-bold">{{ __('web.notary-document-front-uploaded') }}</p>
                                <p>{{ __('web.notary-document-explanation') }}</p>
                            </x-bladewind.alert>
                        </div>
                    @endif

                    <div class="h-2 bg-black w-full rounded-md"></div>

                    @if ($notaryStatus['notary_back']['rejected'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'notary_back']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert type="error" shade="faint" show_close_icon="false"
                                    class="py-4 mb-4">
                                    <p class="mb-4 font-bold">{{ __('web.notary-document-back-rejected') }}</p>
                                    <p>{{ __('web.reason') }}: {{ $notaryStatus['rejection_reason'] }}</p>
                                </x-bladewind.alert>
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="notary_document_back" required
                                                placeholder="{{ __('web.upload-notary-document-back') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif(!$notaryStatus['notary_back']['is_uploaded'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'notary_back']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="notary_document_back" required
                                                placeholder="{{ __('web.upload-notary-document-back') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif($notaryStatus['notary_back']['is_uploaded'] && !$notaryStatus['notary_back']['rejected'])
                        <div class="flex flex-col my-4">
                            <x-bladewind.alert shade="faint" show_close_icon="false" class="py-4">
                                <p class="mb-4 font-bold">{{ __('web.notary-document-back-uploaded') }}</p>
                                <p>{{ __('web.notary-document-explanation') }}</p>
                            </x-bladewind.alert>
                        </div>
                    @endif

                    <div class="h-2 bg-black w-full rounded-md"></div>

                    @if ($notaryStatus['front_side_id']['rejected'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'front_side_id']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert type="error" shade="faint" show_close_icon="false"
                                    class="py-4 mb-4">
                                    <p class="mb-4 font-bold">{{ __('web.front-side-id-rejected') }}</p>
                                    <p>{{ __('web.reason') }}: {{ $notaryStatus['rejection_reason'] }}</p>
                                </x-bladewind.alert>
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="front_side_id" required
                                                placeholder="{{ __('web.upload-front-side-id') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif(!$notaryStatus['front_side_id']['is_uploaded'])
                        <form method="POST"
                            action="{{ route('upload-notary-document', ['orderNo' => $order->order_no, 'type' => 'front_side_id']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col my-4">
                                <x-bladewind.alert shade="dark" show_close_icon="false" class="py-4 mb-2"
                                    show_icon="false">
                                    <p class="font-bold">{{ __('web.notary-file-info') }}</p>
                                    <div class="w-full mt-2">
                                        <div class="w-full bg-white pt-0 rounded-md">
                                            <x-bladewind.filepicker name="front_side_id" required
                                                placeholder="{{ __('web.upload-front-side-id') }}"
                                                accepted_file_types=".pdf, .jpeg, .jpg, .png" />
                                        </div>
                                        <button type="submit"
                                            class="text-white text-left font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full">
                                            <i class="fa-solid fa-cloud"></i> {{ __('web.upload') }}
                                        </button>
                                    </div>
                                </x-bladewind.alert>
                            </div>
                        </form>
                    @elseif($notaryStatus['front_side_id']['is_uploaded'] && !$notaryStatus['front_side_id']['rejected'])
                        <div class="flex flex-col my-4">
                            <x-bladewind.alert shade="faint" show_close_icon="false" class="py-4">
                                <p class="mb-4 font-bold">{{ __('web.front-side-id-uploaded') }}</p>
                                <p>{{ __('web.notary-document-explanation') }}</p>
                            </x-bladewind.alert>
                        </div>
                    @endif
                @endif

                <div class="border-b-2 border-black">
                </div>
                <span class="text-blue-500 font-bold hover:underline cursor-pointer"
                    onclick="showModal('remote-sales-modal')">
                    {{ __('web.distance-sales-contract') }}
                </span>
            </div>
        </div>

        @if ($bondPayments)
            <div class="w-full flex flex-col bg-white p-5 justify-between items-center gap-5">
                <div class="border-b-2 border-black w-full">
                    <span class="text-lg font-bold">{{ __('web.my-payment-plan') }}</span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                    @foreach ($bondPayments as $bondPayment)
                        <x-bladewind.card reduce_padding="true">
                            <div class="flex flex-col gap-3">
                                <div class="grow">
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('web.bond-no') }}:</span>
                                        {{ $bondPayment['e_bond_no'] }}
                                    </p>
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('web.date') }}:</span>
                                        {{ $bondPayment['due_date']->format('d-m-Y') }}
                                    </p>
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('web.payment-amount') }}:</span>
                                        {{ number_format($bondPayment['bond_amount'], 2, ',', '.') }}
                                        TL
                                    </p>
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('web.remaining-amount') }}:</span>
                                        {{ number_format($bondPayment['remaining_amount'], 2, ',', '.') }}
                                        TL
                                    </p>
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('web.paid') }}:</span> <span
                                            class="font-bold {{ !$bondPayment->isPaid() ? 'text-red-400' : 'text-green-500' }}">{{ !$bondPayment->isPaid() ? __('app.no') : __('app.yes') }}</span>
                                    </p>
                                </div>

                                @if (!$bondPayment->isPaid())
                                    <a href="{{ route('sales-agreements.bond-payment-page', ['orderNo' => $order->order_no, 'bond_no' => $bondPayment['e_bond_no']]) }}"
                                        class="bg-green-500 font-bold hover:bg-green-600 rounded-md text-white text-center p-2">
                                        <i class="fa-solid fa-money-bill"></i> {{ __('web.pay-now') }}
                                    </a>
                                @endif
                                <a href="{{ route('sales-agreements.bond-payments-list', ['orderNo' => $order->order_no, 'bond_no' => $bondPayment['e_bond_no']]) }}"
                                    class="bg-blue-500 font-bold hover:bg-blue-600 rounded-md text-white text-center p-2">
                                    <i class="fa-solid fa-list"></i> {{ __('web.show-payments') }}
                                </a>
                            </div>
                        </x-bladewind.card>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="w-full flex flex-col bg-white p-5 justify-between items-center gap-5">
            <div class="border-b-2 border-black w-full">
                <span class="text-lg font-bold">{{ __('web.my-payments') }}</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                @foreach ($order->orderPaymentsWithoutFee as $payment)
                    <x-bladewind.card reduce_padding="true">
                        <div class="flex flex-col gap-3">
                            @if ($payment->bankAccount)
                                <div class="flex items-center w-full justify-center">
                                    <img class="h-[40px] w-auto" style="max-width: 200px;object-fit:contain;"
                                        src="{{ $payment->getBankLogo() }}" />
                                </div>
                            @endif
                            <div class="grow">
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.payment-type') }}:</span>
                                    {{ $payment->getPaymentTypeTranslation() }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.date') }}:</span>
                                    {{ $payment->created_at->format('d-m-Y H:i') }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.payment-amount') }}:</span>
                                    {{ number_format($payment->payment_amount, 2, ',', '.') }}
                                    TL
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.installments') }}:</span>
                                    {{ $payment->number_of_installments }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.approved') }}:</span> <span
                                        class="font-bold {{ $payment->approved_by_erp === 'N' ? 'text-red-400' : 'text-green-500' }}">{{ $payment->approved_by_erp === 'Y' ? __('app.yes') : __('app.no') }}</span>
                                </p>
                                @if ($payment->failed)
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('app.status') }}:</span> <span
                                            class="font-bold text-red-400">{{ __('app.failed-payment') }}</span>
                                    </p>
                                @endif
                            </div>

                            @if ($payment->approved_by_erp === 'N' && !$payment->failed)
                                <form method="POST" class="flex w-full"
                                    action="{{ route('cancel-payment', ['paymentRefNo' => $payment->payment_ref_no]) }}">
                                    @method('post')
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-500 font-bold hover:bg-red-600 rounded-md text-white text-center p-2">
                                        {{ __('web.cancel-payment') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-bladewind.card>
                @endforeach
            </div>
        </div>
    </div>

    <x-bladewind.modal name="remote-sales-modal" title="{{ __('web.remote-sales-agreement') }}" size="omg"
        ok_button_label="{{ __('web.close') }}" cancel_button_label="" body_css="max-h-[80vh] overflow-scroll"
        customActionHref="{{ route('remote-sales-pdf', ['orderNo' => $order->order_no]) }}"
        customActionTitle="{{ __('web.download-pdf') }}">
        <p class="p-2">
            @include('utility.remote-agreement', [
                'dateTime' => $order->created_at->format('d-m-Y H:i:s'),
                'tosAddress' => $order->invoiceUser->address,
                'tosDeliveryAddress' => $order->deliveryUser->address,
                'tosEmail' => $order->invoiceUser->email,
                'tosFullname' => $order->invoiceUser->full_name,
                'tosPhone' => $order->invoiceUser->phone,
                'tosProductName' => $order->productVariation->getDocumentTitle(),
                'tosPrice' => '₺' . number_format($order->total_amount, 2, ',', '.'),
            ])
        </p>
    </x-bladewind.modal>

</x-app-layout>
