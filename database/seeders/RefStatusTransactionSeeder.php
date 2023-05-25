<?php

namespace Database\Seeders;

use App\Models\RefStatusTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefStatusTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RefStatusTransaction::create([
            'name' => 'Menunggu Konfirmasi'
        ]);

        RefStatusTransaction::create([
            'name' => 'Pesanan Diproses'
        ]);

        RefStatusTransaction::create([
            'name' => 'Menunggu Pembayaran'
        ]);

        RefStatusTransaction::create([
            'name' => 'Pesanan Selesai'
        ]);

        RefStatusTransaction::create([
            'name' => 'Pesanan Dibatalkan'
        ]);

        RefStatusTransaction::create([
            'name' => 'Menunggu Konfirmasi UMKM'
        ]);
    }
}
