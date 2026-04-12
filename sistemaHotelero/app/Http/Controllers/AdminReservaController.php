<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('vw_reservas');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $reservas = $query->orderBy('id_reserva', 'desc')->get();

        return view('admin.reservas');
    }
}
