<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateEbondsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ebondsCollection;

    /**
     * Create a new job instance.
     */
    public function __construct($ebondsCollection)
    {
        $this->onQueue('erpjobs');

        $this->ebondsCollection = $ebondsCollection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateEbonds, 'Ebonds', ['ebonds' => $this->ebondsCollection->toArray()]);

            foreach ($this->ebondsCollection as $ebond) {

                try {
                    $erpOrderId = $ebond['erp_order_id'];

                    if (str_contains($erpOrderId, 'SY')) {
                        $erpOrderId = str_replace('SY', '', $erpOrderId);
                        $prefix = 'SY';
                    }

                    if (str_contains($erpOrderId, 'SK')) {
                        $erpOrderId = str_replace('SK', '', $erpOrderId);
                        $prefix = 'SK';
                    }

                    $order = Order::where([
                        ['erp_prefix', $prefix],
                        ['erp_order_id', $erpOrderId],
                    ])->first();

                    if (!$order) {
                        LoggerService::logError(LogChannelsEnum::UpdateEbonds, "Error in handler (Ebond loop): Order not found: {$ebond['erp_order_id']}");
                        continue;
                    }

                    $salesAgreement = $order->salesAgreement;

                    if (!$salesAgreement) {
                        LoggerService::logError(LogChannelsEnum::UpdateEbonds, "Error in handler (Ebond loop): Order has no sales agreement: {$ebond['erp_order_id']}");
                        continue;
                    }

                    $existingEbond = $salesAgreement->ebonds()->where('e_bond_no', $ebond['e_bond_no'])->first();

                    $dueDate = $ebond['due_date'];

                    if ($dueDate) {
                        $dueDate = explode('-', $dueDate);
                        $dueDate = $dueDate[2] . '-' . $dueDate[1] . '-' . $dueDate[0];
                    }

                    if ($existingEbond) {
                        $existingEbond->update([
                            'bond_amount' => $ebond['bond_amount'],
                            'bond_description' => $ebond['bond_description'],
                            'due_date' => $dueDate,
                            'penalty' => $ebond['is_penalty'],
                        ]);

                        if ($ebond['is_penalty'] && !$existingEbond->penalty_sms_sent && !$existingEbond->penalty_email_sent) {
                            $existingEbond->sendPenaltyNotification();

                            $existingEbond->update([
                                'penalty_sms_sent' => true,
                                'penalty_email_sent' => true,
                            ]);
                        }

                        $existingEbond->updateRemainingAmount();
                    } else {
                        $createdEbond = $salesAgreement->ebonds()->create([
                            'e_bond_no' => $ebond['e_bond_no'],
                            'bond_amount' => $ebond['bond_amount'],
                            'remaining_amount' => $ebond['bond_amount'],
                            'bond_description' => $ebond['bond_description'],
                            'due_date' => $dueDate,
                            'penalty' => $ebond['is_penalty'],
                            'order_id' => $order->id,
                            'erp_order_id' => $order->erp_prefix . $order->erp_order_id,
                            'user_id' => $order->invoice_user_id
                        ]);

                        if ($createdEbond && !$salesAgreement->email_sent && !$salesAgreement->sms_sent) {
                            $createdEbond->sendCreatedNotification();

                            $salesAgreement->update([
                                'email_sent' => true,
                                'sms_sent' => true
                            ]);
                        }

                        if ($ebond['is_penalty']) {
                            $createdEbond->sendPenaltyNotification();

                            $createdEbond->update([
                                'penalty_sms_sent' => true,
                                'penalty_email_sent' => true,
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    LoggerService::logError(LogChannelsEnum::UpdateEbonds, "Error in handler (Ebond loop)", [
                        'e' => $e,
                        'ebond' => $ebond ?? null
                    ]);

                    continue;
                }
            }

            LoggerService::logSuccess(LogChannelsEnum::UpdateEbonds, "Ebonds");

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateEbonds, "Error in handler", ['e' => $e]);

            $this->fail();
        }
    }
}
