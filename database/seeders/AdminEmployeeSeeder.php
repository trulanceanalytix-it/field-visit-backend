<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employee_masters')->updateOrInsert(
            ['emp_id' => '99999'],
            [
                'emp_name' => 'Admin',
                'password' => Hash::make('123'),
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
