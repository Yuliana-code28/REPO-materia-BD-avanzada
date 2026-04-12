<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Support\Facades\DB;

class AdminHabitacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Habitacion::with('tipo');
        
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        
        $habitaciones = $query->orderBy('numero_habitacion', 'asc')->get();
        $tipos = TipoHabitacion::all();

        return view('admin.habitaciones', compact('habitaciones', 'tipos'));
    }

    public function formData()
    {
        try {
            $tipos = TipoHabitacion::all();
            return response()->json(['tipos' => $tipos]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apiIndex(Request $request)
    {
        $query = Habitacion::with('tipo');
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        return response()->json($query->orderBy('numero_habitacion', 'asc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_habitacion' => 'required|unique:habitaciones,numero_habitacion|max:10',
            'id_tipo' => 'required|exists:tipos_habitacion,id_tipo',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        Habitacion::create($request->all());

        return redirect()->route('admin.habitaciones')->with('success', 'Habitación creada correctamente.');
    }

    public function update(Request $request, $id)
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

    public function destroy($id)
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
    public function apiStore(Request $request)
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

    public function apiUpdate(Request $request, $id)
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

    public function apiDestroy($id)
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
