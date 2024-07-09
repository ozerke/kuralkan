<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddMissingRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-missing-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns missing roles to users by DB fields (shop & service)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::doesntHave('roles')->get();

        foreach ($users as $user) {
            if ($user->shop == 1 && $user->service == 1) {
                $user->syncRoles([User::ROLES['shop-service']]);
            }

            if ($user->shop != 1 && $user->service == 1) {
                $user->syncRoles([User::ROLES['service']]);
            }

            if ($user->shop == 1 && $user->service != 1) {
                $user->syncRoles([User::ROLES['shop']]);
            }

            if ($user->shop != 1 && $user->service != 1) {
                $user->syncRoles([User::ROLES['customer']]);
            }
        }
    }
}
