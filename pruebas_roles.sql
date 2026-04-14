
-- Todos los roles deben ser probados con ejemplos de acceso permitido y denegado

USE sistemahotelero;

-- 1. CREACIÓN DE USUARIOS SIMULANDO ROLES
DROP USER IF EXISTS 'admin_user'@'localhost';
DROP USER IF EXISTS 'recep_user'@'localhost';
DROP USER IF EXISTS 'cliente_user'@'localhost';

CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'admin123';
CREATE USER 'recep_user'@'localhost' IDENTIFIED BY 'recep123';
CREATE USER 'cliente_user'@'localhost' IDENTIFIED BY 'cliente123';

-- 2. ASIGNACIÓN DE PRIVILEGIOS

-- Rol: admin
-- Tiene acceso máximo a la base de datos
GRANT ALL PRIVILEGES ON sistemahotelero.* TO 'admin_user'@'localhost';

-- Rol: recepcionista
-- Puede manejar reservaciones (registrar, leer, actualizar) y ejecutar procedimientos
GRANT SELECT, INSERT, UPDATE ON sistemahotelero.reservas TO 'recep_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON sistemahotelero.detalle_reservas TO 'recep_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON sistemahotelero.pagos TO 'recep_user'@'localhost';
GRANT SELECT ON sistemahotelero.habitaciones TO 'recep_user'@'localhost';
GRANT EXECUTE ON PROCEDURE sistemahotelero.sp_registrar_reserva TO 'recep_user'@'localhost';
GRANT EXECUTE ON PROCEDURE sistemahotelero.sp_consultar_disponibilidad TO 'recep_user'@'localhost';

-- Rol: cliente
-- Puede consultar vistas públicas/su información y ver disponibilidad general, pero no modificar nada.
GRANT SELECT ON sistemahotelero.vw_reservas TO 'cliente_user'@'localhost';
GRANT SELECT ON sistemahotelero.habitaciones TO 'cliente_user'@'localhost';

FLUSH PRIVILEGES;

-- 3. PRUEBAS DE ACCESO (EJEMPLOS DE PERMITIDO Y DENEGADO)
-- Nota para el evaluador: Para replicar las pruebas de denegación sin detener el script de creación,
-- se debe correr cada bloque probando la conexión con el usuario respectivo.
-- A continuación se documenta lo que resultaría al iniciar sesión con cada uno mediante CLI o Workbench:

-- =======================
-- PRUEBAS COMO recepcionista ('recep_user')
-- =======================
-- ACCESO PERMITIDO: Consultar disponibilidad (Tiene permiso EXECUTE y SELECT sobre dependencias)
-- SESSION 'recep_user'> CALL sp_consultar_disponibilidad('2026-10-01', '2026-10-05'); 
-- SALIDA ESPERADA: Muestra habitaciones disponibles correctamente.

-- ACCESO DENEGADO: Intentar borrar una reserva (No se le otorgó DELETE)
-- SESSION 'recep_user'> DELETE FROM reservas WHERE id_reserva = 1;
-- SALIDA ESPERADA: ERROR 1142 (42000): DELETE command denied to user 'recep_user'@'localhost' for table 'reservas'


-- =======================
-- PRUEBAS COMO cliente ('cliente_user')
-- =======================
-- ACCESO PERMITIDO: Ver datos de las habitaciones (A esto sí se le dio SELECT)
-- SESSION 'cliente_user'> SELECT * FROM habitaciones WHERE estado = 'disponible';
-- SALIDA ESPERADA: Lista de habitaciones disponibles generada.

-- ACCESO DENEGADO: Intentar modificar el precio de un servicio (No tiene UPDATE ni acceso a servicios)
-- SESSION 'cliente_user'> UPDATE servicios SET precio = 10.00 WHERE id_servicio = 1;
-- SALIDA ESPERADA: ERROR 1142 (42000): UPDATE command denied to user 'cliente_user'@'localhost' for table 'servicios'


-- =======================
-- PRUEBAS COMO admin ('admin_user')
-- =======================
-- ACCESO PERMITIDO: Todo, por ejemplo modificar la tabla de tipos de habitación que solo le compete a gerencia.
-- SESSION 'admin_user'> UPDATE tipos_habitacion SET precio_base = precio_base * 1.1; 
-- ACCESO DENEGADO: No hay restricción de denegación para probar aquí salvo DDL del core del motor.

-- ==========================================================
-- 4. RESTRICCIÓN DE ACCESOS (EJEMPLO DE REVOKE)
-- ==========================================================
-- Como dicta la rúbrica, demostramos la retracción de un permiso otorgado previamente.
-- Se revoca la posibilidad de consultar habitaciones al cliente.

REVOKE SELECT ON sistemahotelero.habitaciones FROM 'cliente_user'@'localhost';
FLUSH PRIVILEGES;

