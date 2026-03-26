<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApiUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'g.deleon@jsonplaceholder.test'],
            [
                'name'     => 'G De Leon',
                'password' => Hash::make('applicant@password'),
            ]
        );
    }
}