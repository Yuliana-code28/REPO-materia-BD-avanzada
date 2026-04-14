<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Support\Facades\DB;

class AdminHabitacionController extends Controller
{
    public function mostrarVistaHabitaciones(Request $request)
    {
        $query = Habitacion::with('tipo');
        
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        
        $habitaciones = $query->orderBy('numero_habitacion', 'asc')->get();
        $tipos = TipoHabitacion::all();

        return view('admin.habitaciones', compact('habitaciones', 'tipos'));
    }

    public function obtenerTiposHabitacion()
    {
        try {
            $tipos = TipoHabitacion::all();
            return response()->json(['tipos' => $tipos]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function listarHabitacionesAPI(Request $request)
    {
        // Consulta #8: Obtener habitaciones con información de ocupación actual
        // Se usa una subconsulta para evitar errores si la Vista SQL aún no ha sido creada en la BD.
        $subqueryOcupacion = "
            SELECT 
                h_sub.id_habitacion,
                dr_sub.fecha_inicio,
                dr_sub.fecha_fin,
                CONCAT(c_sub.nombre, ' ', c_sub.ap) AS ocupante
            FROM habitaciones h_sub
            INNER JOIN detalle_reservas dr_sub ON h_sub.id_habitacion = dr_sub.id_habitacion
            INNER JOIN reservas r_sub ON dr_sub.id_reserva = r_sub.id_reserva
            INNER JOIN clientes c_sub ON r_sub.id_cliente = c_sub.id_cliente
            WHERE (CURDATE() BETWEEN dr_sub.fecha_inicio AND dr_sub.fecha_fin)
              AND r_sub.estado = 'activa'
        ";

        $query = DB::table('habitaciones as h')
            ->select('h.*', 'th.nombre_tipo', 'th.precio_base', 'oa.ocupante', 'oa.fecha_inicio', 'oa.fecha_fin')
            ->join('tipos_habitacion as th', 'h.id_tipo', '=', 'th.id_tipo')
            ->leftJoin(DB::raw("($subqueryOcupacion) as oa"), 'h.id_habitacion', '=', 'oa.id_habitacion');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('h.estado', $request->estado);
        }

        $habitaciones = $query->orderBy('h.numero_habitacion', 'asc')->get();

        // Estructurar para mantener compatibilidad con el front (anidar tipo)
        foreach ($habitaciones as $h) {
            $h->tipo = (object)[
                'nombre_tipo' => $h->nombre_tipo,
                'precio_base' => $h->precio_base
            ];
        }

        return response()->json($habitaciones);
    }

    public function guardarHabitacionWeb(Request $request)
    {
        $request->validate([
            'numero_habitacion' => 'required|unique:habitaciones,numero_habitacion|max:10',
            'id_tipo' => 'required|exists:tipos_habitacion,id_tipo',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        Habitacion::create($request->all());

        return redirect()->route('admin.habitaciones')->with('success', 'Habitación creada correctamente.');
    }

    public function actualizarHabitacionWeb(Request $request, $id)
    {
        $habitacion = Habitacion::findOrFail($id);

        $request->validate([
            'numero_habitacion' => 'required|max:10|unique:habitaciones,numero_habitacion,' . $habitacion->id_habitacion . ',id_habitacion',
            'id_tipo' => 'required|exists:tipos_habitacion,id_tipo',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        $habitacion->update($request->all());

        return redirect()->route('admin.habitaciones')->with('success', 'Habitación actualizada correctamente.');
    }

    public function eliminarHabitacionWeb($id)
    {
        $habitacion = Habitacion::findOrFail($id);

        // Prevenir eliminación si existe en detalle_reservas
        $enUso = DB::table('detalle_reservas')->where('id_habitacion', $id)->exists();

        if ($enUso) {
            return redirect()->route('admin.habitaciones')->with('error', 'No se puede eliminar la habitación porque tiene un historial de reservaciones. Cámbiela a estado "mantenimiento".');
        }

        $habitacion->delete();

        return redirect()->route('admin.habitaciones')->with('success', 'Habitación eliminada correctamente.');
    }
    public function crearHabitacionAPI(Request $request)
    {
        try {
            $request->validate([
                'numero_habitacion' => 'required|unique:habitaciones,numero_habitacion|max:10',
                'id_tipo' => 'required|exists:tipos_habitacion,id_tipo',
                'estado' => 'required|in:disponible,ocupada,mantenimiento',
            ]);

            Habitacion::create($request->all());

            return response()->json(['success' => true, 'message' => 'Habitación creada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizarHabitacionAPI(Request $request, $id)
    {
        try {
            $habitacion = Habitacion::findOrFail($id);

            $request->validate([
                'numero_habitacion' => 'sometimes|required|max:10|unique:habitaciones,numero_habitacion,' . $habitacion->id_habitacion . ',id_habitacion',
                'id_tipo' => 'sometimes|required|exists:tipos_habitacion,id_tipo',
                'estado' => 'sometimes|required|in:disponible,ocupada,mantenimiento',
            ]);

            $habitacion->update($request->all());

            return response()->json(['success' => true, 'message' => 'Habitación actualizada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function eliminarHabitacionAPI($id)
    {
        try {
            $habitacion = Habitacion::findOrFail($id);

            $enUso = DB::table('detalle_reservas')->where('id_habitacion', $id)->exists();

            if ($enUso) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar la habitación porque tiene reservas asociadas.']);
            }

            $habitacion->delete();

            return response()->json(['success' => true, 'message' => 'Habitación eliminada correctamente.']);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
