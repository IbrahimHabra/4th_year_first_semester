<?php

namespace Database\Seeders;

use App\Models\ModifyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['Added', 'Consumed', 'Wasted', 'Refunded'];

        foreach ($types as $name) {
            ModifyType::updateOrCreate(['name' => $name]);
        }
    }
}
