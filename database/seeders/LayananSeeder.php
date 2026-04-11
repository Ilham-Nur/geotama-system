<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['MT', 'PT', 'UT'];

        foreach ($data as $nama) {
            Layanan::firstOrCreate(['nama' => $nama]);
        }
    }
}