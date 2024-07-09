@foreach ($campaigns as $campaign)
<div class="mt-[10px] border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
    <div class="flex flex-col text-center gap-4">
            <div class="w-full">
                {{ __('web.campaign-details', ['installments' => $campaign->installments, 'downPaymentAmount' => $campaign->getDownPaymentAmount('',true), 'rate' => number_format($campaign->rate, 0, "", ".")]) }}
            </div>
    </div>
</div>
@endforeach
