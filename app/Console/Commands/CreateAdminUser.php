<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Email');
        $password = $this->ask('Password');
        $firstname = $this->ask('First name');
        $lastname = $this->ask('Last name');

        $exists = User::where('email', $email)->exists();

        if ($exists) {
            $this->error('User with this email exists');
            return;
        }

        User::create([
            'email' => $email,
            'password' => $password,
            'site_user_name' => $firstname,
            'site_user_surname' => $lastname,
            'district_id' => 1,
            'phone' => '+111000111',
            'date_of_birth' => "1900-01-01",
        ])->assignRole(User::ROLES['admin']);

        $this->info('Admin was created');
    }
}
