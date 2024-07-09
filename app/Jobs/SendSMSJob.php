<?php

namespace App\Jobs;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected ?string $orderNo;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message, ?string $orderNo = null)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->orderNo = $orderNo;
    }

    public function backoff(): array
    {
        return [3, 5, 10];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            SMSService::sendSMS($this->phone, $this->message);
            LoggerService::logSuccess(LogChannelsEnum::MessagesSms, 'SMS Sent', ['order_no' => $this->orderNo, 'phone' => $this->phone, 'message' => $this->message]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::MessagesSms, 'Error in handler', ['e' => $e, 'order_no' => $this->orderNo, 'phone' => $this->phone, 'message' => $this->message]);
        }
    }
}
