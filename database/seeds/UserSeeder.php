<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\user;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create(['name' => 'Super admin']);
        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'normal']);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@appserver.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('Super admin');
    }
}
