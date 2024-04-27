<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "nama" => $this->nama,
            "nis" => $this->nis,
            "jenis_kelamin" => $this->jenis_kelamin,
            "id_kelas" => $this->kelas,
            "kontak" => $this->kontak,
            "kontak_orang_tua" => $this->kontak_orang_tua,
            "alamat" => $this->alamat,
            "token"=> $this->token,
        ];
    }
}
