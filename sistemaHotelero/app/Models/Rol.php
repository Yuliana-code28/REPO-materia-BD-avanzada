<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    
    protected $primaryKey = 'id_rol';

    public $timestamps = false;

    protected $fillable = [
        'nombre_rol'
    ];

    public function usuarios(){
        return $this->hasMany(Usuarios::class, 'id_rol');
    }

}
