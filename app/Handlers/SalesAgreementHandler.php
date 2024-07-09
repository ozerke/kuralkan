<?php

namespace App\Handlers;

use App\Jobs\Orders\SalesAgreements\FindeksRequestStatusJob;
use App\Jobs\Orders\SalesAgreements\InitiateFindeksRequestJob;
use App\Models\SalesAgreement;

class SalesAgreementHandler
{
    protected SalesAgreement $salesAgreement;

    public function __construct(SalesAgreement $salesAgreement)
    {
        $this->salesAgreement = $salesAgreement;
    }

    /*
    This handler will provide flow management for Sales Agreement process after the SA entity has been created in the system (plan was selected).

    Explanations:
    - SA = Sales Agreement

    First two stages are managed by the controller direct to ERP, after they are passed, the whole process is controlled by this handler.

    Stage transitions and lifecycles:
        1. Initial (plan not selected / user not verified, no existing SA entity for order)
        2. Application fee (plan selected, fee not paid, SA entity exists for order)
        3. Initiate FINDEKS request (fee paid, no present findeks_request_id)
        4. Initiate FINDEKS request status (findeks ID exists, SA is not yet approved/rejected, findeks_request_status is not 5) - limited times of retries until marked as failed until next handler call
        5. SMS PIN pending (findeks_request_status is 3, SA is not yet approved/rejected) - calls 4. stage again
        6. Initiate FINDEKS request result (findeks_request_status is 5, SA is not yet approved/rejected)
        7. Initiate FINDEKS merge order (findeks_request_result is 1, SA is not yet approved/rejected)
        8. Initiate application rejected (findeks_request_result is 0, SA is not yet approved/rejected)
        9. Collect down payment (findeks_merged_order is 1, SA is approved)
        10. Initiate SA document request (Order is paid, SA is approved, and there is no document URL present)
        11. After the document reaches the database, or the ERP did not send us the link, we destroy the Sales Agreement Stage entity from DB for optimization

    Frontend architecture:
        The processing screen will be the same for every Job related action, only seperate screens will be for:
            - application fee
            - SMS PIN verification
            - rejected application
            - collecting down payment
            - thank you page

    Check frontend response: 
            - stage: string
            - redirectToRoute: null | string
    */

    public function getCurrentStage()
    {
        return $this->salesAgreement->stage;
    }

    public function getExpectedStage()
    {
        if (empty($this->salesAgreement->application_fee_payment_id)) {
            return SalesAgreement::STAGES['application_fee'];
        }

        if (empty($this->salesAgreement->findeks_request_id)) {
            return SalesAgreement::STAGES['initiate_findeks'];
        }

        /* Not approved sequence */
        if ($this->salesAgreement->isNotApproved()) {

            if ($this->salesAgreement->retry_count >= config('app.max_sa_retries', 5)) {
                return SalesAgreement::STAGES['retry_later'];
            }

            if (!$this->salesAgreement->findeks_request_status || !in_array($this->salesAgreement->findeks_request_status, [3, 5])) {
                return SalesAgreement::STAGES['findeks_request_status'];
            }

            if ($this->salesAgreement->findeks_request_status == 3) {
                return SalesAgreement::STAGES['sms_pin_pending'];
            }

            if ($this->salesAgreement->findeks_request_status == 5) {
                if (is_null($this->salesAgreement->findeks_request_result)) {
                    return SalesAgreement::STAGES['findeks_request_result'];
                }

                if (!is_null($this->salesAgreement->findeks_request_result) && !$this->salesAgreement->findeks_request_result) {
                    return SalesAgreement::STAGES['declined'];
                }

                if (!$this->salesAgreement->findeks_merged_order) {
                    return SalesAgreement::STAGES['findeks_merge_order'];
                }
            }
        }

        /* Rejected sequence */
        if ($this->salesAgreement->isDeclined()) {
            return SalesAgreement::STAGES['declined'];
        }

        /* Approved sequence */
        if ($this->salesAgreement->isApproved()) {
            $paidState = $this->salesAgreement->order->getOrderPaymentsState();

            if ($paidState && $paidState['is_paid']) {
                return SalesAgreement::STAGES['finished'];
            }

            return SalesAgreement::STAGES['collect_down_payment'];
        }
    }

    public function navigateUserToStage()
    {
        $stage = $this->getExpectedStage();

        $redirectableStages = [
            SalesAgreement::STAGES['application_fee'],
            SalesAgreement::STAGES['retry_later'],
            SalesAgreement::STAGES['sms_pin_pending'],
            SalesAgreement::STAGES['declined'],
            SalesAgreement::STAGES['collect_down_payment'],
            SalesAgreement::STAGES['finished']
        ];

        if (in_array($stage, $redirectableStages)) {
            return $this->handleRedirect($stage);
        }

        return $this->handleProcessingScreen($stage);
    }

    public function getRouteForStage($stage)
    {
        switch ($stage) {
            case SalesAgreement::STAGES['application_fee']:
                return route('sales-agreements.application-fee', ['orderNo' => $this->salesAgreement->order->order_no]);
            case SalesAgreement::STAGES['retry_later']:
                return route('sales-agreements.retry-later', ['orderNo' => $this->salesAgreement->order->order_no]);
            case SalesAgreement::STAGES['sms_pin_pending']:
                return route('sales-agreements.findeks-sms-pin', ['orderNo' => $this->salesAgreement->order->order_no]);
            case SalesAgreement::STAGES['declined']:
                return route('sales-agreements.application-rejected', ['orderNo' => $this->salesAgreement->order->order_no]);
            case SalesAgreement::STAGES['collect_down_payment']:
                return route('sales-agreements.collect-down-payment', ['orderNo' => $this->salesAgreement->order->order_no]);
            case SalesAgreement::STAGES['finished']:
                return route('sales-agreements.thank-you', ['orderNo' => $this->salesAgreement->order->order_no]);

            default:
                return null;
        }
    }

    private function handleRedirect($stage)
    {
        $route = $this->getRouteForStage($stage);

        return redirect($route);
    }

    protected function handleProcessingScreen($stage)
    {
        if ($stage === SalesAgreement::STAGES['initiate_findeks']) {
            if ($this->getCurrentStage() !== SalesAgreement::STAGES['initiate_findeks']) {
                $this->salesAgreement->update([
                    'stage' => SalesAgreement::STAGES['initiate_findeks']
                ]);
            }

            dispatch(new InitiateFindeksRequestJob($this->salesAgreement->order))->delay(now()->addSeconds(1));
        }

        if ($stage === SalesAgreement::STAGES['findeks_request_status']) {

            if ($this->getCurrentStage() !== SalesAgreement::STAGES['findeks_request_status']) {
                $this->salesAgreement->update([
                    'stage' => SalesAgreement::STAGES['findeks_request_status']
                ]);
            }

            dispatch(new FindeksRequestStatusJob($this->salesAgreement->id))->delay(now()->addSeconds(1));
        }

        return redirect()->route('sales-agreements.processing', ['orderNo' => $this->salesAgreement->order->order_no]);
    }
}
