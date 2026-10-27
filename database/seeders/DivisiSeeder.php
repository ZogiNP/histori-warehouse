<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach ([
                'Penyiap Reseller',
                'Quality Control',
                'Penerima Barang',
            ] as $nama) {
                Divisi::firstOrCreate([
                    'nama_divisi' => $nama,
                ]);
            }
        });
    }
}