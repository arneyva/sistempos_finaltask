<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expense::firstOrCreate([
            'date' => '2022-11-11 19:12:55',
            'Ref' => 'EXP_001',
            'user_id' => 1,
            'expense_category_id' => 1,
            'warehouse_id' => 1,
            'details' => 'Apapun itu',
            'amount' => 500000,
            'status' => 0,
        ]);
    }
}
