<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Valencia',
            'location' => 'Valencia, Carabobo',
        ]);

        Warehouse::create([
            'name' => 'La Guaira',
            'location' => 'La Guaira, Vargas',
        ]);

        Warehouse::create([
                'name' => 'Caracas',
                'location' => 'Caracas, Distrito Capital',
        ]);
        Warehouse::create([
                'name' => 'Transit',
                'location' => 'Transit',
        ]);
    }
}
