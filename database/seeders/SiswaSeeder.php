<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $kelas = Kelas::first();
        for ($i=0; $i < 36; $i++) { 
            Siswa::create([
                "nama" => 'nama_siswa-'.$i,
                "nis" => "0977288".$i,
                "jenis_kelamin" => "L",
                "id_kelas" =>  $kelas->id,
                "kontak" => "08386367".$i,
                "kontak_orang_tua" => "0997377".$i,
                "alamat" => "alamat_siswa-".$i,
                "password"=> Hash::make('test'.$i),
                "token"=> 'test'.$i
            ]);
        }
        
    }
}
