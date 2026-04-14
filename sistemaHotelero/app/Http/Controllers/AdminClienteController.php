<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminClienteController extends Controller
{
    public function mostrarVistaClientes()
    {
        return view('admin.clientes');
    }

    public function listarClientesAPI(Request $request)
    {
        // 1. Uso de Vista + CASE para clasificación
        $clientesSQL = "
            SELECT 
                v.id_cliente,
                v.cliente as nombre_completo, 
                c.email,
                c.telefono,
                v.total_reservas,
                v.total_pagado,
                CASE 
                    WHEN v.total_reservas >= 5 THEN 'CLIENTE DIAMANTE'
                    WHEN v.total_reservas >= 3 THEN 'CLIENTE PLATINO'
                    WHEN v.total_reservas >= 1 THEN 'CLIENTE ESTANDAR'
                    ELSE 'CLIENTE NUEVO'
                END AS clasificacion
            FROM vw_historial_clientes v
            JOIN clientes c ON v.id_cliente = c.id_cliente
        ";
        
        $clientes = DB::select($clientesSQL);

        // 2. Uso de Subconsulta para encontrar Mejores Pagadores (Top Payers) - Consulta #3
        $topPayersSQL = "
            SELECT id_cliente
            FROM vw_historial_clientes
            WHERE total_pagado > (SELECT AVG(monto) FROM pagos)
        ";
        $topPayersRaw = DB::select($topPayersSQL);
        $topPayersIds = array_map(function($o){ return $o->id_cliente; }, $topPayersRaw);

        // 3. Uso de Subconsulta para encontrar Clientes Frecuentes - Consulta #7
        $frecuentesSQL = "
            SELECT id_cliente 
            FROM vw_historial_clientes 
            WHERE total_reservas > (SELECT AVG(total_reservas) FROM (
                SELECT fn_total_reservas_cliente(id_cliente) AS total_reservas FROM clientes
            ) AS sub)
        ";
        $frecuentesRaw = DB::select($frecuentesSQL);
        $frecuentesIds = array_map(function($o){ return $o->id_cliente; }, $frecuentesRaw);

        // Mapear validaciones al arreglo final
        foreach ($clientes as $c) {
            $c->es_top_pagador = in_array($c->id_cliente, $topPayersIds);
            $c->es_frecuente = in_array($c->id_cliente, $frecuentesIds);
        }

        return response()->json($clientes);
    }
}
