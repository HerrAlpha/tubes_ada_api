<?php

namespace Database\Seeders;

use App\Models\RefSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RefSetting::create([
            'name'  => 'Keuntungan Admin',
            'key'   => 'profit_admin',
            'value' => 20
        ]);

        RefSetting::create([
            'name'  => 'Keuntungan UMKM',
            'key'   => 'profit_enterprise',
            'value' => 40
        ]);

        RefSetting::create([
            'name'  => 'Keuntungan Investor',
            'key'   => 'profit_investor',
            'value' => 40
        ]);

        RefSetting::create([
            'name'  => 'Margin Price Product',
            'key'   => 'margin_price_product',
            'value' => 20
        ]);

        RefSetting::create([
            'name'  => 'Account Bank Name',
            'key'   => 'account_bank_name',
            'value' => 'Lorem Ipsum Dolor Sit Amet'
        ]);

        RefSetting::create([
            'name'  => 'Account Bank Number',
            'key'   => 'account_bank_number',
            'value' => '4678365123'
        ]);
    }
}
