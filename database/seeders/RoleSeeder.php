<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (User::ROLES as $role) {
            if (Role::where('name', $role)->exists()) {
                continue;
            }

            Role::create(['name' => $role]);
        }
    }
}
