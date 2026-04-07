<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Usuarios extends Model
{
    protected $table = 'usuarios';
    use HasApiTokens;
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'contrasena',
        'id_rol',
        'id_cliente',
        'id_empleado'
    ];

    public function rol(){
        return $this->belongsTo(Rol::class,'id_rol');
    }

    public function empleado(){
        return $this->belongsTo(Empleados::class,'id_cliente');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class,'id_cliente');
    }


}
