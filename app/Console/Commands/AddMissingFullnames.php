<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddMissingFullnames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-missing-fullnames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns missing fullnames to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('fullname')->get();

        foreach ($users as $user) {
            $user->update([
                'fullname' => $user->site_user_name . ' ' . $user->site_user_surname
            ]);
        }
    }
}
