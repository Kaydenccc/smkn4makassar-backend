<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absen extends Model
{
    use HasFactory;
    protected $table = 'absens';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'id_guru','id_siswa',"id_kelas", "id_mapel", 'status', "tanggal", "keterangan",'materi'
    ];

    public function guru(): BelongsTo {
        return $this->belongsTo(Guru::class, 'id_guru', 'id');
    }
    public function siswa(): BelongsTo {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }
    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
    public function mapel(): BelongsTo {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id');
    }
}
