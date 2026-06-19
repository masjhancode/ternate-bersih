<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin Utama
        User::updateOrCreate(
            ['email' => 'yasirmuin@unkhair.ac.id'],
            [
                'name' => 'Yasir Muin',
                'password' => bcrypt('password'), // Password default: password
                'role' => 'Administrator',
                'nik' => '0000000000000000',
                'phone_number' => '081234567890',
                'address' => 'Kantor DLH Kota Ternate',
            ]
        );
        
        // Buat akun Driver (Sopir) untuk testing aplikasi mobile
        User::updateOrCreate(
            ['email' => 'driver@ternatebersih.com'],
            [
                'name' => 'Sopir Andalan',
                'password' => bcrypt('password'),
                'role' => 'Driver Armada',
                'nik' => '1111111111111111',
                'phone_number' => '089876543210',
                'address' => 'Pangkalan Truk DLH',
            ]
        );

        // --- MASTER DATA REGION (WILAYAH) ---
        // Jika tabel District masih kosong, buat data awal
        if (\App\Models\District::count() === 0) {
            $ternateTengah = \App\Models\District::create(['name' => 'Ternate Tengah']);
            $ternateSelatan = \App\Models\District::create(['name' => 'Ternate Selatan']);
            $ternateUtara = \App\Models\District::create(['name' => 'Ternate Utara']);

            // Buat Kelurahan untuk Ternate Tengah
            $ternateTengah->villages()->createMany([
                ['name' => 'Muhajirin'],
                ['name' => 'Maliaro'],
                ['name' => 'Takoma']
            ]);

            // Buat Kelurahan untuk Ternate Selatan
            $ternateSelatan->villages()->createMany([
                ['name' => 'Jati'],
                ['name' => 'Bastiong Karance'],
                ['name' => 'Mangga Dua']
            ]);

            // Buat Kelurahan untuk Ternate Utara
            $ternateUtara->villages()->createMany([
                ['name' => 'Dufa Dufa'],
                ['name' => 'Sangaji'],
                ['name' => 'Akehuda']
            ]);
        }

        // --- MASTER DATA KATEGORI LAPORAN ---
        if (\App\Models\ReportCategory::count() === 0) {
            \App\Models\ReportCategory::insert([
                ['name' => 'Tumpukan Liar'],
                ['name' => 'TPS Penuh'],
                ['name' => 'Sampah Sungai/Pantai'],
            ]);
        }
    }
}
