<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        $adminRole = Role::where('name','Admin')->first();

        User::create([

            'role_id'=>$adminRole->id,

            'name'=>'Administrator',

            'email'=>'admin@mystock.test',

            'password'=>'password',

            'is_active'=>true
        ]);
    }
}