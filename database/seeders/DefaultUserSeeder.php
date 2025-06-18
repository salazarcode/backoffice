<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'salazarcode@gmail.com'],
            ['name' => 'Andrés Salazar','password' => Hash::make('Java***174') ]
        );
    }
}
