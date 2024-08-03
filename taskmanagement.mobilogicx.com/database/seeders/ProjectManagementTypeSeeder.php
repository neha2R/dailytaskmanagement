<?php

namespace Database\Seeders;

use App\Models\ProjectManagementType;
use Illuminate\Database\Seeder;

class ProjectManagementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProjectManagementType::create([
            'slug'=>'division',
            'name'=>"Division",
        ]);
        ProjectManagementType::create([
            'slug'=>'subdivision',
            'name'=>"Sub Division",
        ]);
        ProjectManagementType::create([
            'slug'=>'site',
            'name'=>"Site",
        ]);
    }
}
