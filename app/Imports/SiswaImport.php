<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Siswa;

class SiswaImport implements ToModel
{
    public function model(array $row)
    {
        return new Siswa([
            'nama' => $row[0],
            'nis' => $row[1],
            'jenis_kelamin' => $row[2],
            'id_kelas' => $row[3],
            'kontak' => $row[4],
            'kontak_orang_tua' => $row[5],
            'alamat' => $row[6],
        ]);
    }
}
