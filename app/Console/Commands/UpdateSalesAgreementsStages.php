<?php

namespace App\Console\Commands;

use App\Handlers\SalesAgreementHandler;
use App\Models\SalesAgreement;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\Output;

class UpdateSalesAgreementsStages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-agreements:update-stages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates and updates old sales agreements to new structure (stages)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $salesAgreements = SalesAgreement::where('stage', 'application_fee')->get();

        foreach ($salesAgreements as $sa) {
            if ($sa->order) {
                $this->updateAgreementByStage($sa);
            }
        }

        $this->output->success('Finished update');
    }

    private function updateAgreementByStage($salesAgreement)
    {
        if (!$salesAgreement->isDeclined() && $salesAgreement->is_sms_pending) {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['sms_pin_pending'],
                'findeks_request_status' => 3,
                'findeks_request_result' => 0,
                'findeks_merged_order' => 0
            ]);

            $this->output->info("Updating SA ID: {$salesAgreement->id} | Adjusting as SMS PENDING");

            return;
        }

        if ($salesAgreement->isDeclined()) {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['declined'],
                'findeks_request_status' => null,
                'findeks_request_result' => null,
                'findeks_merged_order' => null
            ]);

            $this->output->info("Updating SA ID: {$salesAgreement->id} | Adjusting as DECLINED");

            return;
        }

        $handler = new SalesAgreementHandler($salesAgreement);
        $stage = $handler->getExpectedStage();

        if ($stage == SalesAgreement::STAGES['finished']) {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['finished'],
                'findeks_request_status' => 5,
                'findeks_request_result' => 1,
                'findeks_merged_order' => 1
            ]);

            $this->output->info("Updating SA ID: {$salesAgreement->id} | Adjusting as FINISHED");

            return;
        }

        if ($stage == SalesAgreement::STAGES['collect_down_payment']) {
            $salesAgreement->update([
                'stage' => SalesAgreement::STAGES['collect_down_payment'],
                'findeks_request_status' => 5,
                'findeks_request_result' => 1,
                'findeks_merged_order' => 1
            ]);

            $this->output->info("Updating SA ID: {$salesAgreement->id} | Adjusting as COLLECT DOWN PAYMENT");

            return;
        }

        return;
    }
}
