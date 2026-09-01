<?php

namespace Database\Seeders;

use App\Models\PassType;
use Illuminate\Database\Seeder;

class PassTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['type' => 'monthly', 'duration_months' => 1,  'price' => 150.00],
            ['type' => 'monthly', 'duration_months' => 2,  'price' => 280.00],
            ['type' => 'monthly', 'duration_months' => 3,  'price' => 400.00],
            ['type' => 'monthly', 'duration_months' => 6,  'price' => 750.00],
            ['type' => 'monthly', 'duration_months' => 12, 'price' => 1400.00],
            ['type' => 'single', 'duration_months' => null, 'price' => 25.00],
        ];

        foreach ($defaults as $default) {
            PassType::firstOrCreate(
                ['type' => $default['type'], 'duration_months' => $default['duration_months']],
                ['price' => $default['price']]
            );
        }
    }
}
