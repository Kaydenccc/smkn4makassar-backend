<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Guru;

class GuruImport implements ToModel
{
    public function model(array $row)
    {
        return new Guru([
            'nama' => $row[0],
            'nip' => $row[1],
            'email' => $row[2],
            'password' => $row[3],
            'no_hp' => $row[4],
        ]);
    }
}
