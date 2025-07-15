<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TUGAS 1: Buat user default (penting untuk semua lingkungan)
        User::firstOrCreate(
            ['email' => 'superadmin@rawconnect.test'], // Kunci untuk mencari
            values: [ // Data yang akan dibuat jika tidak ditemukan
            'name' => 'Feygi Setiawan',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'mfaisalfahri02@gmail.com'], // Kunci untuk mencari
            values: [ // Data yang akan dibuat jika tidak ditemukan
            'name' => 'M. Faisal Fahri',
            'password' => Hash::make('12345678'),
            'role' => 'pegawai',
            ]
        );

        // TUGAS 2: Panggil seeder data contoh HANYA JIKA lingkungan adalah 'local'
        // if (app()->environment('local')) {
        //     $this->call([
        //         ProdukSeeder::class,
        //     ]);
        // }
    }
}