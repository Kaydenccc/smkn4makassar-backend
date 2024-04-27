<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsenResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'id_guru'=> $this->guru,
            'id_siswa'=> $this->siswa,
            "id_kelas"=> $this->kelas, 
            "id_mapel"=> $this->mapel, 
            'status'=> $this->status, 
            "jam"=> $this->jam,
            "tanggal"=> $this->tanggal,
            "keterangan"=> $this->keterangan,
            'materi'=> $this->materi
        ];
    }
}
