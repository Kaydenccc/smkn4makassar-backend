<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements Authenticatable
{
    use HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = 'admins';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',"password"
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }
     public function getAuthIdentifier()
     {
        return $this->id;
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
