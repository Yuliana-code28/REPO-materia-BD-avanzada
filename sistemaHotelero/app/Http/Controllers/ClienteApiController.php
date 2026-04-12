<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use Illuminate\Support\Facades\DB;

class ClienteApiController extends Controller
{
    /**
     * Obtenemos el resumen para el dashboard del cliente.
     */
    public function getDashboardSummary(Request $request)
    {
        $user = $request->user();
        if (!$user->id_cliente) {
            return response()->json(['error' => 'Usuario no vinculado a un cliente'], 403);
        }

        $id_cliente = $user->id_cliente;

        // Estancia actual o próxima (Reserva activa o pendiente más cercana)
        $estanciaProxima = DB::table('vw_reservas')
            ->where('email_cliente', $user->username) // O buscar por id_cliente si la vista lo tiene
            // Nota: La vista no tiene id_cliente, pero podemos usar el email si coincide con el username
            // o mejor consultamos la tabla real.
            ->get();
            
        // Re-pensando: Usaremos el id_cliente directamente sobre el modelo Reserva.
        $reservaActiva = Reserva::where('id_cliente', $id_cliente)
            ->where('estado', 'activa')
            ->with(['habitaciones', 'detalleReservas'])
            ->first();

        $proximaReserva = Reserva::where('id_cliente', $id_cliente)
            ->where('estado', 'pendiente')
            ->orderBy('id_reserva', 'asc')
            ->with(['habitaciones', 'detalleReservas'])
            ->first();

        $totalGastado = DB::table('pagos')
            ->join('reservas', 'pagos.id_reserva', '=', 'reservas.id_reserva')
            ->where('reservas.id_cliente', $id_cliente)
            ->sum('pagos.monto');

        // Obtener consumos de todas las reservas activas/pendientes del cliente
        $consumos = DB::table('consumos_servicios')
            ->join('servicios', 'consumos_servicios.id_servicio', '=', 'servicios.id_servicio')
            ->join('reservas', 'consumos_servicios.id_reserva', '=', 'reservas.id_reserva')
            ->join('detalle_reservas', 'reservas.id_reserva', '=', 'detalle_reservas.id_reserva')
            ->join('habitaciones', 'detalle_reservas.id_habitacion', '=', 'habitaciones.id_habitacion')
            ->where('reservas.id_cliente', $id_cliente)
            ->whereIn('reservas.estado', ['activa', 'pendiente'])
            ->select(
                'servicios.nombre_servicio', 
                'consumos_servicios.cantidad', 
                'servicios.precio', 
                'consumos_servicios.fecha_consumo',
                'habitaciones.numero_habitacion'
            )
            ->orderBy('consumos_servicios.fecha_consumo', 'desc')
            ->get();
        
        $totalServicios = $consumos->sum(function($c) {
            return $c->cantidad * $c->precio;
        });

        return response()->json([
            'activa' => $reservaActiva,
            'proxima' => $proximaReserva,
            'totalGastado' => $totalGastado,
            'totalReservas' => Reserva::where('id_cliente', $id_cliente)->count(),
            'consumos' => $consumos,
            'totalServicios' => $totalServicios,
            'limiteAlcanzado' => (Reserva::where('id_cliente', $id_cliente)->whereIn('estado', ['activa', 'pendiente'])->count() >= 4)
        ]);
    }

    /**
     * Listado completo de reservas del cliente.
     */
    public function getReservas(Request $request)
    {
        $user = $request->user();
        $id_cliente = $user->id_cliente;

        $reservas = Reserva::where('id_cliente', $id_cliente)
            ->with(['habitaciones', 'pagos', 'detalleReservas'])
            ->orderBy('id_reserva', 'desc')
            ->get();

        return response()->json($reservas);
    }

    /**
     * El cliente registra su propia reserva.
     */
    public function storePropiaReserva(Request $request)
    {
        $user = $request->user();
        $id_cliente = $user->id_cliente;

        if (!$id_cliente) {
            return response()->json(['success' => false, 'message' => 'Usuario no vinculado a un cliente'], 403);
        }

        // VALIDACIÓN DE LÍMITE: Máximo 4 activas/pendientes
        $reservasActuales = Reserva::where('id_cliente', $id_cliente)
            ->whereIn('estado', ['activa', 'pendiente'])
            ->count();
        
        if ($reservasActuales >= 4) {
            return response()->json([
                'success' => false, 
                'message' => 'Límite alcanzado: Tienes ' . $reservasActuales . ' reservaciones activas. Por favor libera o finaliza una antes de reservar otra.'
            ], 422);
        }

        try {
            $request->validate([
                'id_habitacion' => 'required|integer',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'metodo_pago' => 'required|string|max:50',
            ]);

            // Primero calculamos el costo para registrar el pago
            $costoData = DB::selectOne(
                'SELECT fn_calcular_costo_proyectado(?, ?, ?) AS costo', 
                [$request->id_habitacion, $request->fecha_inicio, $request->fecha_fin]
            );

            if (!$costoData || $costoData->costo === null) {
                return response()->json(['success' => false, 'message' => 'No se pudo calcular el costo de la estancia.'], 400);
            }

            // Llamamos al SP de registro (el mismo que usa admin pero con id_cliente del token)
            DB::statement('CALL sp_registrar_reserva(?, ?, ?, ?, ?, ?)', [
                $id_cliente,
                $request->id_habitacion,
                $request->fecha_inicio,
                $request->fecha_fin,
                $costoData->costo,
                $request->metodo_pago
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Tu reservación ha sido confirmada!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la reserva: ' . $e->getMessage()
            ], 500);
        }
    }
}
