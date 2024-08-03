<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            'Toll Expense',
            'Fuel & Urea Expense',
            'Food Expense',
            'Challans',
            'Maintenance',
            'Miscellaneous'
        ];
        foreach ($data as $key => $value) {
            $slug = Str::slug($value, '-');
            Expense::create([
                'name' => $value,
                'slug' =>  $this->makeUniqueSlug($slug),
                'is_active' => true,
                'iconPath' => "",
            ]);
        }
    }
    private function makeUniqueSlug($proposedSlug)
    {
        $slug = $proposedSlug;
        $count = 1;
        while (Expense::where('slug', $slug)->exists()) {
            $slug = $proposedSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}
