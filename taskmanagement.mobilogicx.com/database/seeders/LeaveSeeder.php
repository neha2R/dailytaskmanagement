<?php

namespace Database\Seeders;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Leave::create([
            'user_id'=>2,
            'start_date'=>Carbon::now(),
            'end_date'=>Carbon::now()->addDay(2),
            'description'=>'seeder desc',
            'days'=>2
        ]);
    }
}
