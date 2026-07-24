<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'paul.arsolon@deped.gov.ph'],
            [
                'name' => 'Paul Arsolon',
                'email' => 'paul.arsolon@deped.gov.ph',
                'password' => 'oat2026',
                'bio_id' => 'ADMIN001',
                'tag' => 1,
                'role' => 'super_admin',
            ]
        );
    }
}
