<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Product;

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
        Role::create(['name' => 'mesero']);

        $user = User::create([
            'name' => 'Sarita',
            'email' => 'adminsarita@appserver.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('Super admin');

        $product = Product::create([
            'name'=> 'Dulcesito corazón',
            'measure' => 'Dulcesito corazón',
            'price' => 1,
            'type' => 'Dulcesito corazón'
        ]);
    }
}
