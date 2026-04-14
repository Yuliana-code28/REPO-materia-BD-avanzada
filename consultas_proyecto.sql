
-- CONSULTAS DEL PROYECTO FINAL - SISTEMA HOTELERO


-- 1. Reservas con costo
-- Objetivo: Listar todas las reservas incluyendo el costo calculado por la función.
SELECT 
    id_reserva, 
    nombre_cliente, 
    fecha_inicio, 
    fecha_fin, 
    fn_costo_estancia(id_reserva) AS costo_calculado
FROM vw_reservas;

-- 2. Clientes clasificados
-- Objetivo: Categorizar a los clientes según su volumen de reservas usando CASE.
SELECT 
    cliente, 
    total_reservas,
    CASE 
        WHEN total_reservas >= 5 THEN 'CLIENTE DIAMANTE'
        WHEN total_reservas >= 3 THEN 'CLIENTE PLATINO'
        WHEN total_reservas >= 1 THEN 'CLIENTE ESTANDAR'
        ELSE 'CLIENTE NUEVO'
    END AS clasificacion
FROM vw_historial_clientes;

-- 3. Clientes con pago mayor al promedio (Subconsulta)
-- Objetivo: Identificar clientes que han gastado más que el promedio general.
SELECT cliente, total_pagado
FROM vw_historial_clientes
WHERE total_pagado > (SELECT AVG(monto) FROM pagos);

-- 4. Ingresos por periodo (Mensuales con ingresos superiores a $5,000)
-- Objetivo: Reportar el total de dinero ingresado agrupado por mes, filtrando solo los meses de altas ganancias mediante HAVING.
SELECT 
    DATE_FORMAT(fecha_pago, '%Y-%m') AS periodo,
    SUM(monto) AS ingresos_totales
FROM pagos
GROUP BY periodo
HAVING ingresos_totales > 5000
ORDER BY periodo DESC;

-- 5. Mostrar reservas activas con cliente y habitación (JOIN)
-- Objetivo: Vista rápida para recepción de quién está actualmente en qué habitación.
SELECT 
    r.id_reserva,
    c.nombre,
    c.ap,
    h.numero_habitacion,
    dr.fecha_inicio,
    dr.fecha_fin
FROM reservas r
JOIN clientes c ON r.id_cliente = c.id_cliente
JOIN detalle_reservas dr ON r.id_reserva = dr.id_reserva
JOIN habitaciones h ON dr.id_habitacion = h.id_habitacion
WHERE r.estado = 'activa';

-- 6. Calcular ingresos totales por periodo (Anual)
-- Objetivo: Resumen ejecutivo de ingresos por año.
SELECT 
    YEAR(fecha_pago) AS anio,
    SUM(monto) AS total_anual
FROM pagos
GROUP BY anio;

-- 7. Mostrar clientes con más reservas que el promedio (Subconsulta)
-- Objetivo: Clientes frecuentes que superan la media de reservas.
SELECT cliente, total_reservas
FROM vw_historial_clientes
WHERE total_reservas > (SELECT AVG(total_reservas) FROM (
    SELECT fn_total_reservas_cliente(id_cliente) AS total_reservas FROM clientes
) AS sub);

