<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetSalesShopsPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-sales-shops-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets passwords to ERP User ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shops = User::shops()->get();
        $services = User::services()->get();

        foreach ($shops as $shop) {
            $shop->update([
                'password' => $shop->erp_user_id ?: $shop->email
            ]);
        }

        foreach ($services as $shop) {
            $shop->update([
                'password' => $shop->erp_user_id ?: $shop->email
            ]);
        }

        $this->info('Updated passwords');
    }
}
