<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFacturacionController extends Controller
{
    public function index()
    {
        return view('admin.facturacion');
    }

    public function apiIndex(Request $request)
    {
        // Consulta avanzada con JOIN para el historial de facturación
        $pagosSQL = "
            SELECT 
                p.id_pago,
                p.id_reserva,
                CONCAT(c.nombre, ' ', c.ap) as cliente,
                p.monto,
                p.fecha_pago,
                p.metodo_pago
            FROM pagos p
            JOIN reservas r ON p.id_reserva = r.id_reserva
            JOIN clientes c ON r.id_cliente = c.id_cliente
            ORDER BY p.fecha_pago DESC
        ";

        $pagos = DB::select($pagosSQL);
        return response()->json($pagos);
    }

    public function apiReportes()
    {
        // 1. Ingresos Mensuales (Consulta #4 del script SQL)
        $mensualSQL = "
            SELECT 
                DATE_FORMAT(fecha_pago, '%Y-%m') AS periodo,
                SUM(monto) AS ingresos_totales
            FROM pagos
            GROUP BY periodo
            ORDER BY periodo DESC
            LIMIT 6
        ";
        
        // 2. Ingresos Anuales (Consulta #6 del script SQL)
        $anualSQL = "
            SELECT 
                YEAR(fecha_pago) AS anio,
                SUM(monto) AS total_anual
            FROM pagos
            GROUP BY anio
            ORDER BY anio DESC
        ";

        // 3. Totales generales para tarjetas
        $stats = [
            'total_historico' => DB::table('pagos')->sum('monto'),
            'pago_promedio' => DB::table('pagos')->avg('monto'),
            'mensual' => DB::select($mensualSQL),
            'anual' => DB::select($anualSQL)
        ];

        return response()->json($stats);
    }
}
