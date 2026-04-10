<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    use HasFactory;

    protected $table = 'detalle_reservas';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'id_habitacion',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion', 'id_habitacion');
    }
}

