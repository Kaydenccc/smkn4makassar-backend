<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model implements Authenticatable
{
    use HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = 'gurus';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'nama',"nip","email","password",'no_hp'
    ];

 
    public function getAuthIdentifierName()
    {
        return 'nip';
    }
     public function getAuthIdentifier()
     {
        return $this->nip;
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
