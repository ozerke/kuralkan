<?php

namespace App\Jobs;

use App\Mail\AfterDueDateMail;
use App\Mail\EbondsCreatedMail;
use App\Mail\OnBeforeDueDateMail;
use App\Mail\OnDueDateMail;
use App\Mail\PenaltyMail;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEbondEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $ebond;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
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
     *
     * @return void
     */
    public function handle()
    {
        try {
            $mail = null;
            $email = $this->ebond->user->email;

            if (!$email) {
                LoggerService::logError(LogChannelsEnum::MessagesEmail, '[EBOND] User has no email',  ['e_bond_no' => $this->ebond->e_bond_no, 'user_id' => $this->ebond->user->id]);
            }

            switch ($this->type) {
                case 'created':
                    $mail = new EbondsCreatedMail($this->ebond);
                    break;
                case 'beforeDueDate':
                    $mail = new OnBeforeDueDateMail($this->ebond);
                    break;
                case 'onDueDate':
                    $mail = new OnDueDateMail($this->ebond);
                    break;
                case 'afterDueDate':
                    $mail = new AfterDueDateMail($this->ebond);
                    break;
                case 'penalty':
                    $mail = new PenaltyMail($this->ebond);
                    break;

                default:
                    LoggerService::logError(LogChannelsEnum::MessagesEmail, '[EBOND] Unsupported type',  ['e_bond_no' => $this->ebond->e_bond_no, 'type' => $this->type]);
                    break;
            }

            if ($mail) {
                Mail::to($email)->send($mail);
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::MessagesEmail, 'Error in handler', ['e' => $e]);
        }
    }
}
