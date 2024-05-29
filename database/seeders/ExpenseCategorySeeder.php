<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::firstOrCreate([
            'user_id' => 181,
            'name' => "Dekorasi",
            'description' => "Menanamkan Pohon di sekitaran",
        ]);
    }
}
