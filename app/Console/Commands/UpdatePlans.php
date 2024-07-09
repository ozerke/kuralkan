<?php

namespace App\Console\Commands;

use App\Jobs\Products\TriggerPlansUpdate;
use Illuminate\Console\Command;

class UpdatePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product plans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new TriggerPlansUpdate);

        $this->info('Job dispatched');
    }
}
