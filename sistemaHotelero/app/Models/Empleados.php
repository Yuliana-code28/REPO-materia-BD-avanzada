<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ap',
        'am',
        'telefono',
        'email'
    ];

    public function usuario()
    {
        return $this->hasOne(Usuarios::class, 'id_empleado');
    }
}
