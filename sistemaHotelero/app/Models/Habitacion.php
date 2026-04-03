<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';
    protected $primaryKey = 'id_habitacion';
    public $timestamps = false;

    protected $fillable = [
        'numero_habitacion',
        'id_tipo',
        'estado',
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoHabitacion::class, 'id_tipo', 'id_tipo');
    }
}
