<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "kelas"
    ];
    
    public function absens(): HasMany {
        return $this->hasMany(Absen::class, 'id_kelas', 'id');
    }
    public function siswas(): HasMany {
        return $this->hasMany(Siswa::class, 'id_siswa', 'id');
    }

}
