<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'uuid' => 'usr1234567890',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email' => 'superAdmin@gmail.com',
            'phone' => '9090909090',
            'password' => Hash::make('1234567890'),
            'role'=> 'SA',
            'phone_verified'=>'YES',
            'email_verified'=>'YES',
            'image'=>'default.png',
            'created_at'=>date('Y-m-d H:i:s')
        ]);
    }
}
