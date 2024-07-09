<?php

namespace App\Jobs;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $to;
    protected $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $email)
    {
        $this->to = $to;
        $this->email = $email;
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
            Mail::to($this->to)->send($this->email);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::MessagesEmail, 'Error in handler', ['e' => $e]);
        }
    }
}
