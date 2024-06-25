<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::firstOrCreate([
            'user_id' => 1,
            'name' => 'Dekorasi',
            'description' => 'Menanamkan Pohon di sekitaran',
        ]);
    }
}
