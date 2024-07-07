<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::firstOrCreate([
            'email' => 'tokopedai085179750679@gmail.com',
            'currency_id' => 1,
            'sms_gateway' => 1,
            'is_invoice_footer' => 0,
            'invoice_footer' => null,
            'warehouse_id' => null,
            'CompanyName' => 'ProjectTA',
            'CompanyPhone' => '081921731912',
            'CompanyAdress' => 'Jebres,Surakarta',
            'footer' => '',
            'developed_by' => '',
            'logo' => 'logo-default.png',
        ]);
    }
}
