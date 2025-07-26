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
        User::firstOrCreate(
            ['email' => 'superadmin@rawconnect.test'], 
            values: [ 
            'name' => 'Feygi Setiawan',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'mfaisalfahri02@gmail.com'], 
            values: [
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