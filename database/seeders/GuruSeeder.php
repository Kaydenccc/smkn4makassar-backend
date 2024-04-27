<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Guru::create([
            "nama"=> 'test',
            "nip"=> 'nip191993',
            "email"=> 'test@test.com',
            "password"=> Hash::make('test'),
            "no_hp"=>"02928829291",
            "token"=> 'test'
        ]);
    }
}
