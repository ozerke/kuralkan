<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    const USERS = [
        'admin' => [
            'email' => 'admin@ekuralkan.com',
            'password' => 'IevbeYzYcFRH',
            'site_user_name' => 'Admin',
            'site_user_surname' => 'User',
            'role' => User::ROLES['admin'],
            'district_id' => 1,
            'phone' => '+111000111',
            'date_of_birth' => "1900-01-01",
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::USERS as $user) {
            $exists = User::where('email', $user['email'])->exists();
            if ($exists) {
                echo "\033 skipping \033\n";
                continue;
            }

            $data = $user;
            unset($data['role']);

            User::create($data)->assignRole($user['role']);
        }
    }
}
