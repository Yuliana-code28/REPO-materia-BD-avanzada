<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservaApiController extends Controller
{

    public function listarReservas(Request $request)
    {
        $hoy = date('Y-m-d');

        // AUTO-SINCRONIZACIÓN:

        // 1. Activar reservas pendientes que inician hoy (y que no han vencido)
        DB::table('reservas')
            ->where('estado', 'pendiente')
            ->whereIn('id_reserva', function ($q) use ($hoy) {
                $q->select('id_reserva')->from('detalle_reservas')
                    ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
            })
            ->update(['estado' => 'activa']);

        // 2. Finalizar reservas que ya terminaron (fecha_fin < hoy)
        DB::table('reservas')
            ->where('estado', 'activa')
            ->whereIn('id_reserva', function ($q) use ($hoy) {
                $q->select('id_reserva')->from('detalle_reservas')
                    ->where('fecha_fin', '<', $hoy);
            })
            ->update(['estado' => 'finalizada']);

        // 3. Liberar habitaciones que ya no tienen reservas activas hoy
        DB::table('habitaciones')
            ->where('estado', 'ocupada')
            ->whereNotIn('id_habitacion', function ($q) use ($hoy) {
                $q->select('dr.id_habitacion')
                    ->from('detalle_reservas as dr')
                    ->join('reservas as r', 'dr.id_reserva', '=', 'r.id_reserva')
                    ->where('r.estado', 'activa')
                    ->where('dr.fecha_inicio', '<=', $hoy)
                    ->where('dr.fecha_fin', '>=', $hoy);
            })
            ->update(['estado' => 'disponible']);

        // 4. Asegurar que habitaciones con reservas activas hoy estén como 'ocupada'
        DB::table('habitaciones')
            ->whereIn('id_habitacion', function ($q) use ($hoy) {
                $q->select('dr.id_habitacion')
                    ->from('detalle_reservas as dr')
                    ->join('reservas as r', 'dr.id_reserva', '=', 'r.id_reserva')
                    ->where('r.estado', 'activa')
                    ->where('dr.fecha_inicio', '<=', $hoy)
                    ->where('dr.fecha_fin', '>=', $hoy);
            })
            ->update(['estado' => 'ocupada']);

        $query = DB::table('vw_reservas');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $reservas = $query->orderBy('id_reserva', 'desc')->get();

        return response()->json($reservas);
    }

    public function obtenerDatosFormularioReserva()
    {
        $clientes = \App\Models\Cliente::all();
        $habitaciones = \App\Models\Habitacion::with('tipo')->where('estado', '!=', 'mantenimiento')->get();

        return response()->json([
            'clientes' => $clientes,
            'habitaciones' => $habitaciones
        ]);
    }

    public function calcularCostoEstancia(Request $request)
    {
        $request->validate([
            'id_habitacion' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        try {
            $costo = DB::selectOne(
                'SELECT fn_calcular_costo_proyectado(?, ?, ?) AS costo',
                [$request->id_habitacion, $request->fecha_inicio, $request->fecha_fin]
            );

            return response()->json([
                'success' => true,
                'costo' => $costo->costo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular el costo.'
            ], 500);
        }
    }

    public function consultarDisponibilidadHabitaciones(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        try {
            $habitaciones = DB::select('CALL sp_consultar_disponibilidad(?, ?)', [
                $request->fecha_inicio,
                $request->fecha_fin
            ]);

            return response()->json($habitaciones);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registrarNuevaReserva(Request $request)
    {
        try {
            $request->validate([
                'id_cliente' => 'required|integer',
                'id_habitacion' => 'required|integer',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'monto_pago' => 'required|numeric',
                'metodo_pago' => 'required|string|max:50',
            ]);

            DB::statement('CALL sp_registrar_reserva(?, ?, ?, ?, ?, ?)', [
                $request->id_cliente,
                $request->id_habitacion,
                $request->fecha_inicio,
                $request->fecha_fin,
                $request->monto_pago,
                $request->metodo_pago
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservación creada exitosamente.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            $errorMsg = 'Ocurrió un error inesperado al procesar la reserva.';
            $rawMsg = $e->getMessage();

            // Si es un error arrojado por SIGNAL (código 1644)
            if (strpos($rawMsg, '1644') !== false) {
                // Extraer el texto a partir del '1644 '
                $parts = explode('1644', $rawMsg);
                if (count($parts) > 1) {
                    // Remover detalles finales de la conexión (Connection: mysql...)
                    $errorMsg = explode(' (Connection:', trim($parts[1]))[0];
                }
            } else {
                $errorMsg = $rawMsg;
            }

            return response()->json([
                'success' => false,
                'message' => $errorMsg
            ], 500);
        }
    }


    public function cancelarReserva($id)
    {
        try {
            DB::beginTransaction();

            // 1. Actualizar estado de la reserva
            $affected = DB::table('reservas')
                ->where('id_reserva', $id)
                ->update(['estado' => 'cancelada']);

            if ($affected === 0) {
                throw new \Exception("No se encontró la reservación o ya está cancelada.");
            }

            // 2. Liberar la habitación vinculada
            // Buscamos el detalle para saber qué habitación liberar
            $detalle = DB::table('detalle_reservas')
                ->where('id_reserva', $id)
                ->first();

            if ($detalle) {
                DB::table('habitaciones')
                    ->where('id_habitacion', $detalle->id_habitacion)
                    ->update(['estado' => 'disponible']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservación cancelada correctamente y habitación liberada.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finalizarReserva($id)
    {
        try {
            DB::beginTransaction();

            // 1. Actualizar estado de la reserva
            $affected = DB::table('reservas')
                ->where('id_reserva', $id)
                ->update(['estado' => 'finalizada']);

            if ($affected === 0) {
                throw new \Exception("No se encontró la reservación o ya no está activa.");
            }

            // 2. Liberar la habitación vinculada
            $detalle = DB::table('detalle_reservas')
                ->where('id_reserva', $id)
                ->first();

            if ($detalle) {
                DB::table('habitaciones')
                    ->where('id_habitacion', $detalle->id_habitacion)
                    ->update(['estado' => 'disponible']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservación finalizada correctamente y habitación liberada.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar: ' . $e->getMessage()
            ], 500);
        }
    }
}
