<?php

namespace App\Jobs;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSService;
use App\Services\SMSTemplateParser;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEbondSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ebond;
    protected $type;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($ebond, $type)
    {
        $this->ebond = $ebond;
        $this->type = $type;
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
            $message = null;

            $phone = $this->ebond->user->phone;

            if (!$phone) {
                LoggerService::logError(LogChannelsEnum::MessagesSms, '[EBOND] User has no phone number',  ['e_bond_no' => $this->ebond->e_bond_no, 'type' => $this->type, 'user_id' => $this->ebond->user->id]);
            }

            switch ($this->type) {
                case 'created':
                    $message = SMSTemplateParser::ebondsCreated(
                        $this->ebond->user->full_name
                    );
                    break;
                case 'beforeDueDate':
                    $message = SMSTemplateParser::beforeDueDate(
                        $this->ebond->user->full_name,
                        $this->ebond->e_bond_no,
                        $this->ebond->due_date->format('d-m-Y'),
                        $this->ebond->bond_amount,
                    );
                    break;
                case 'onDueDate':
                    $message = SMSTemplateParser::onDueDate(
                        $this->ebond->user->full_name,
                        $this->ebond->e_bond_no,
                        $this->ebond->due_date->format('d-m-Y'),
                        $this->ebond->bond_amount,
                    );
                    break;
                case 'afterDueDate':
                    $message = SMSTemplateParser::afterDueDate(
                        $this->ebond->user->full_name,
                        $this->ebond->e_bond_no,
                        $this->ebond->due_date->format('d-m-Y'),
                        $this->ebond->bond_amount,
                    );
                    break;
                case 'penalty':
                    $message = SMSTemplateParser::ebondPenalty(
                        $this->ebond->user->full_name,
                        $this->ebond->e_bond_no,
                        $this->ebond->due_date->format('d-m-Y'),
                        $this->ebond->bond_amount,
                    );
                    break;

                default:
                    LoggerService::logError(LogChannelsEnum::MessagesSms, '[EBOND] Unsupported type',  ['e_bond_no' => $this->ebond->e_bond_no, 'type' => $this->type]);
                    break;
            }

            if ($message) {
                SMSService::sendSMS($phone, $message);
                LoggerService::logSuccess(LogChannelsEnum::MessagesSms, '[EBOND] SMS Sent', ['e_bond_no' => $this->ebond->e_bond_no, 'message' => $message]);
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::MessagesSms, '[EBOND] Error in handler',  ['e_bond_no' => $this->ebond->e_bond_no, 'message' => $message ?? '', 'e' => $e]);
        }
    }
}
