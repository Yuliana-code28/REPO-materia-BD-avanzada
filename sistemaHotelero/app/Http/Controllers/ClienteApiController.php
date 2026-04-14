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
    public function obtenerResumenDashboard(Request $request)
    {
        $user = $request->user();
        if (!$user->id_cliente) {
            return response()->json(['error' => 'Usuario no vinculado a un cliente'], 403);
        }

        $id_cliente = $user->id_cliente;

        $this->sincronizarReservas($id_cliente);

        // Estancia actual o próxima (Reserva activa o pendiente más cercana)
        $estanciaProxima = DB::table('vw_reservas')
            ->where('email_cliente', $user->username) // O buscar por id_cliente si la vista lo tiene
            ->get();

        //  Usaremos el id_cliente directamente sobre el modelo Reserva.
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

        $totalServicios = $consumos->sum(function ($c) {
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
    public function listarReservas(Request $request)
    {
        $user = $request->user();
        $id_cliente = $user->id_cliente;

        $this->sincronizarReservas($id_cliente);

        $reservas = Reserva::where('id_cliente', $id_cliente)
            ->with(['habitaciones', 'pagos', 'detalleReservas'])
            ->orderBy('id_reserva', 'desc')
            ->get();

        return response()->json($reservas);
    }

    /**
     * El cliente registra su propia reserva.
     */
    public function crearReservaPropia(Request $request)
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

            // Llamamos al SP de registro 
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

    /**
     * Sincronización automática de estados para el cliente actual.
     */
    private function sincronizarReservas($id_cliente)
    {
        $hoy = date('Y-m-d');

        // 1. Activar reservas pendientes que inician hoy (y que no han vencido)
        DB::table('reservas')
            ->where('id_cliente', $id_cliente)
            ->where('estado', 'pendiente')
            ->whereIn('id_reserva', function ($q) use ($hoy) {
                $q->select('id_reserva')->from('detalle_reservas')
                    ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
            })
            ->update(['estado' => 'activa']);

        // 2. Finalizar reservas que ya terminaron (fecha_fin < hoy)
        DB::table('reservas')
            ->where('id_cliente', $id_cliente)
            ->where('estado', 'activa')
            ->whereIn('id_reserva', function ($q) use ($hoy) {
                $q->select('id_reserva')->from('detalle_reservas')
                    ->where('fecha_fin', '<', $hoy);
            })
            ->update(['estado' => 'finalizada']);

        // 3. Liberar habitaciones que ya no tienen reservas activas hoy
        DB::table('habitaciones')
            ->where('estado', 'ocupada')
            ->whereIn('id_habitacion', function ($q) use ($id_cliente) {
                $q->select('id_habitacion')->from('detalle_reservas')
                    ->whereIn('id_reserva', function ($q2) use ($id_cliente) {
                        $q2->select('id_reserva')->from('reservas')
                            ->where('id_cliente', $id_cliente);
                    });
            })
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
            ->whereIn('id_habitacion', function ($q) use ($id_cliente, $hoy) {
                $q->select('dr.id_habitacion')
                    ->from('detalle_reservas as dr')
                    ->join('reservas as r', 'dr.id_reserva', '=', 'r.id_reserva')
                    ->where('r.id_cliente', $id_cliente)
                    ->where('r.estado', 'activa')
                    ->where('dr.fecha_inicio', '<=', $hoy)
                    ->where('dr.fecha_fin', '>=', $hoy);
            })
            ->update(['estado' => 'ocupada']);
    }
}
