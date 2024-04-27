<?php
namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GuruImport;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;


class ExcelServiceGuru
{
    public function parseAndSaveExcel($file)
    {
        // Validasi dan proses upload file menggunakan library Laravel Excel
        $import = new GuruImport();
        $data = Excel::toCollection($import, $file)->first();

        // Simpan data ke database
        // dd($data->slice(1));
        foreach ($data->slice(1) as $guru) {
            Guru::create([
                'nama' => $guru[0],
                'nip' => $guru[1],
                'email' => $guru[2],
                'password' => Hash::make($guru[3]),
                'no_hp' => $guru[4],
                // Sesuaikan dengan kolom lain yang ada di model Siswa
            ]);
        }

        return $data;
    }
}