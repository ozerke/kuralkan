@if (!$order->orderCampaign && !$order->hasPayments())
    <div class="flex flex-col gap-4 lg:gap-2 w-full bg-white rounded-md p-4 shadow-md moving-border">
        <h4 class="font-bold text-xl">{{ __('web.campaigns') }}</h4>
        <div class="flex flex-col gap-8 min-h-[50px] lg:items-start mt-4">
            @foreach ($campaigns as $campaign)
                <x-bladewind.radio-button color="blue"
                    label="{{ __('web.campaign-details', ['installments' => $campaign->installments, 'downPaymentAmount' => $campaign->getDownPaymentAmount($order), 'rate' => $campaign->rate]) }}"
                    name="campaign" value="{{ $campaign->id }}" labelCss="!mb-0 !text-[18px] font-bold"
                    meta="{{ $campaign->getDownPaymentAmount($order, false) }}" />
            @endforeach
            <x-bladewind.radio-button checked="true" color="blue" label="{{ __('web.decline-campaign-selection') }}"
                name="campaign" value="" labelCss="!mb-0 !text-[18px] font-bold" />
        </div>
    </div>
@elseif($order->orderCampaign)
    <div class="flex flex-col gap-2 w-full bg-white rounded-md p-4 shadow-md moving-border">
        <h4 class="font-bold text-xl">{{ __('web.your-campaign') }}</h4>

        <div class="flex flex-col lg:items-start mt-2 !mb-0 !text-[18px] font-bold">
            {{ __('web.campaign-details', ['installments' => $order->orderCampaign->installments, 'downPaymentAmount' => $order->orderCampaign->down_payment, 'rate' => $order->orderCampaign->rate]) }}
        </div>

        @if ($order->orderCampaign->is_down_payment_bank)
            <div
                class="flex flex-col lg:items-start mt-2 !mb-0 !text-[18px] font-bold py-2 px-4 bg-blue-500 rounded-md text-white">
                {{ __('web.campaign-details-bank-payment', ['downPaymentAmount' => $order->orderCampaign->down_payment]) }}
            </div>
        @endif
    </div>
@endif
