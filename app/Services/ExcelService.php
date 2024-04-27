<?php
namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;


class ExcelService
{
    public function parseAndSaveExcel($file)
    {
        // Validasi dan proses upload file menggunakan library Laravel Excel
        $import = new SiswaImport();
        $data = Excel::toCollection($import, $file)->first();

        // Simpan data ke database
        // dd($data->slice(1));
        foreach ($data->slice(1) as $siswa) {
            Siswa::create([
                'nama' => $siswa[0],
                'nis' => $siswa[1],
                'jenis_kelamin' => $siswa[2],
                'id_kelas' => $siswa[3],
                'kontak' => $siswa[4],
                'kontak_orang_tua' => $siswa[5],
                'alamat' => $siswa[6],
                'password' => Hash::make($siswa[7]),
                // Sesuaikan dengan kolom lain yang ada di model Siswa
            ]);
        }

        return $data;
    }
}