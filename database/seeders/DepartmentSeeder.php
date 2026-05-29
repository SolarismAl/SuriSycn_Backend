<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'IT Department', 'description' => 'Information Technology'],
            ['name' => 'HR Department', 'description' => 'Human Resources'],
            ['name' => 'Finance', 'description' => 'Finance and Accounting'],
        ];

        foreach ($departments as $dept) {
            \App\Models\Department::create($dept);
        }
    }
}
