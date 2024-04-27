<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    use HasFactory;
    protected $primaryKey = "id";
    protected $keyType = "int";
    protected $table = 'mapels';
    public $timestamps = true;
    public $incrementing = true;

   protected $fillable = [
        "mapel"
   ];
   
    public function absens(): HasMany {
        return $this->hasMany(Absen::class, 'id_mapel', 'id');
    }
}
