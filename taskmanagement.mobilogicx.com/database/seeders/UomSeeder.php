<?php

namespace Database\Seeders;

use App\Models\Uom;
use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  $data = ['Kilogram', 'Units', 'Gram (g)', 'Meter', 'Litre', 'Foot (feet)', 'Inches (inch)'];
        foreach ($data as $key => $value) {
            Uom::create([
                'name'=>$value
            ]);
        }
    }
}
