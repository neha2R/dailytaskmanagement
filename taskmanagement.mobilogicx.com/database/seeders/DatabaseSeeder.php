<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\InventoryType;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // User::create([
        //     'name'=>'Admin',
        //     'email'=>'admin@gmail.com',
        //     'password'=>Hash::make('admin@2023'),
        //     'mobile'=> 9057580843
        // ]);
        $this->call(DocInsert::class);
        $this->call(ExpenseSeeder::class);
        $this->call(InventoryTypeSeeder::class);
        $this->call(ProjectManagementTypeSeeder::class);
        $this->call(UomSeeder::class);
        // Department::create(['name'=>'testDep']);
        // Role::create(['name'=>'testRole']);
        // Position::create(['name'=>'testPos']);
    }
}
