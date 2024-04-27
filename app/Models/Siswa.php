<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa  extends Model implements Authenticatable
{
    use HasFactory;
    protected $table = 'siswas';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "nama",
        "nis",
        "password",
        "jenis_kelamin",
        "id_kelas",
        "kontak",
        "kontak_orang_tua",
        "alamat",
    ];

    public function absens(): HasMany {
        return $this->hasMany(Absen::class, 'id_siswa', 'id');
    }
    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    public function getAuthIdentifierName()
    {
        return 'nip';
    }
     public function getAuthIdentifier()
     {
        return $this->nis;
     }
     public function getAuthPassword()
     {
        return $this->password;
     }

     public function getRememberToken()
     {
        return $this->token;
     }
     public function setRememberToken($value)
     {
        return $this->token = $value;
     }

     public function getRememberTokenName()
     {
        return 'token';
     }

}
