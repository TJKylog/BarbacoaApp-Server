<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\Product;
use App\InvoiceCount;
use App\UserLastname;

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
        Role::create(['name' => 'Super administrador']);
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Invitado']);
        Role::create(['name' => 'Mesero']);

        $user = User::create([
            'name' => 'Sarita',
            'email' => 'adminsarita@appserver.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $lastname = UserLastname::create([
            'user_id' => $user->id,
            'first_lastname' => "Apellido paterno",
            'second_lastname' => 'Apellido materno'
        ]);

        $user->assignRole('Super administrador');

        $product = Product::create([
            'name'=> 'Dulcesito corazón',
            'measure' => 'Dulcesito corazón',
            'price' => 1,
            'type' => 'Dulcesito corazón'
        ]);
        $invoice = InvoiceCount::create([
            'invoice_count' => 1
        ]);
    }
}
