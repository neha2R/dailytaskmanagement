<?php

namespace Database\Seeders;

use App\Models\InventoryType;
use Illuminate\Database\Seeder;

class InventoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InventoryType::create([
                'slug'=>'warehouse',
                'name'=>'Warehouse',
            ]);
        InventoryType::create([
                'slug'=>'depot',
                'name'=>"Depo't",
            ]);
        InventoryType::create([
                'slug'=>'site',
                'name'=>"Site",
            ]);
    }
}
