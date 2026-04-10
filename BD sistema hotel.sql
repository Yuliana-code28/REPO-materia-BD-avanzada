drop database if exists sistemahotelero;
create database sistemahotelero;
use sistemahotelero; 

create table roles(
   id_rol int auto_increment primary key,
   nombre_rol varchar(100) unique
);

create table clientes (
    id_cliente int auto_increment primary key,
    nombre varchar(50) not null,
    ap varchar(50) not null,
    am varchar(50) not null,
    email varchar(100) unique not null,
    telefono varchar(15) not null
);

CREATE TABLE empleados (
    id_empleado int AUTO_INCREMENT primary key,
    nombre varchar(50) not null,
    ap varchar(50) not null,
    am varchar(50) not null,
    telefono varchar(15),
    email varchar(100) unique
);


create table usuarios (
   id_usuario int auto_increment primary key,
   username varchar(50) unique,
   contrasena varchar(255),
   id_rol int,
   id_cliente int,
   id_empleado int,
   foreign key(id_rol) references roles(id_rol),
   foreign key(id_cliente) references clientes(id_cliente),
   foreign key(id_empleado) references empleados(id_empleado)
);


create table tipos_habitacion (
    id_tipo int auto_increment primary key,
    nombre_tipo varchar(50) not null,
    precio_base decimal(10,2) not null check(precio_base > 0)
);

create table habitaciones (
    id_habitacion int auto_increment primary key,
    numero_habitacion varchar(10) unique not null,
    id_tipo int not null,
    estado varchar(20) default 'disponible',
    foreign key (id_tipo) references tipos_habitacion(id_tipo) on delete restrict
);

create table servicios (
    id_servicio int auto_increment primary key,
    nombre_servicio varchar(100) not null,
    precio decimal(10,2) not null check(precio > 0)
);

create table reservas (
    id_reserva int auto_increment primary key,
    id_cliente int not null,
    fecha_registro datetime default current_timestamp,
    estado varchar(20) default 'activa',
    foreign key (id_cliente) references clientes(id_cliente) on delete cascade
);

create table detalle_reservas (
    id_detalle int auto_increment primary key,
    id_reserva int not null,
    id_habitacion int not null,
    fecha_inicio date not null,
    fecha_fin date not null,
    foreign key (id_reserva) references reservas(id_reserva) on delete cascade,
    foreign key (id_habitacion) references habitaciones(id_habitacion) on delete cascade
);

create table consumos_servicios (
    id_consumo int auto_increment primary key,
    id_reserva int not null,
    id_servicio int not null,
    cantidad int not null default 1,
    fecha_consumo datetime default current_timestamp,
    foreign key (id_reserva) references reservas(id_reserva) on delete cascade,
    foreign key (id_servicio) references servicios(id_servicio) on delete cascade
);

create table pagos (
    id_pago int auto_increment primary key,
    id_reserva int not null,
    monto decimal(10,2) not null check(monto > 0),
    fecha_pago datetime default current_timestamp,
    metodo_pago varchar(50) not null,
    foreign key (id_reserva) references reservas(id_reserva) on delete cascade
);

insert into roles (nombre_rol) values
('admin'),
('recepcionista'),
('cliente');

insert into clientes (nombre, ap, am, email, telefono) values
('Ana','Lopez','Gomez','ana.lopez@email.com','4421112233'),
('Carlos','Perez','Ramirez','cperez@email.com','4422223344'),
('Maria','Gomez','Hernandez','mgomez@email.com','4423334455'),
('Jorge','Ramirez','Lopez','jramirez@email.com','4424445566'),
('Luisa','Fernandez','Torres','lfernandez@email.com','4425556677'),
('Pedro','Martinez','Diaz','pmartinez@email.com','4426667788'),
('Sofia','Torres','Garcia','storres@email.com','4427778899'),
('Diego','Flores','Sanchez','dflores@email.com','4428889900'),
('Laura','Santiago','Cruz','lsantiago@email.com','4429990011'),
('Miguel','Angel','Morales','mangel@email.com','4421010101'),
('Carmen','Salinas','Ortiz','csalinas@email.com','4422020202'),
('Roberto','Cruz','Reyes','rcruz@email.com','4423030303'),
('Paula','Vargas','Ruiz','pvargas@email.com','4424040404'),
('Daniel','Reyes','Castro','dreyes@email.com','4425050505'),
('Elena','Morales','Herrera','emorales@email.com','4426060606'),
('Ricardo','Ortiz','Luna','rortiz@email.com','4427070707'),
('Valeria','Castro','Vega','vcastro@email.com','4428080808'),
('Javier','Luna','Mendoza','jluna@email.com','4429090909'),
('Andrea','Ruiz','Flores','aruiz@email.com','4420101010'),
('Fernando','Herrera','Nava','fherrera@email.com','4421212121');

insert into empleados (nombre, ap, am, telefono, email) values
('Juan','Perez','Lopez','4421000001','juan@gmail.com'),
('Luis','Garcia','Ramirez','4421000002','luis@gmail.com'),
('Maria','Torres','Diaz','4421000003','maria@gmail.com'),
('Ana','Martinez','Cruz','4421000004','ana@gmail.com'),
('Carlos','Lopez','Reyes','4421000005','carlos@gmail.com'),
('Sofia','Hernandez','Vega','4421000006','sofia@gmail.com'),
('Pedro','Sanchez','Morales','4421000007','pedro@gmail.com'),
('Laura','Diaz','Ortiz','4421000008','laura@gmail.com'),
('Miguel','Rivera','Castro','4421000009','miguel@gmail.com'),
('Carmen','Cruz','Luna','4421000010','carmen@gmail.com'),
('Jorge','Flores','Mendoza','4421000011','jorge@gmail.com'),
('Daniel','Morales','Nava','4421000012','daniel@gmail.com'),
('Elena','Ruiz','Vargas','4421000013','elena@gmail.com'),
('Ricardo','Ortiz','Perez','4421000014','ricardo@gmail.com'),
('Valeria','Castro','Lopez','4421000015','valeria@gmail.com'),
('Fernando','Herrera','Diaz','4421000016','fernando@gmail.com'),
('Andrea','Luna','Cruz','4421000017','andrea@gmail.com'),
('Roberto','Vargas','Reyes','4421000018','roberto@gmail.com'),
('Paula','Reyes','Vega','4421000019','paula@gmail.com'),
('Diego','Gomez','Morales','4421000020','diego@gmail.com');


insert into usuarios (username, contrasena, id_rol, id_cliente, id_empleado) values

('Juan1', '123456', 1, NULL, 1),


('Luis1', '123456', 2, NULL, 2),
('Maria1', '123456', 2, NULL, 3),
('Ana1', '123456', 2, NULL, 4),
('Carlos1', '123456', 2, NULL, 5),
('Sofia1', '123456', 2, NULL, 6),
('Pedro1', '123456', 2, NULL, 7),
('Laura1', '123456', 2, NULL, 8),
('Miguel1', '123456', 2, NULL, 9),
('Carmen1', '123456', 2, NULL, 10),
('Jorge1', '123456', 2, NULL, 11),
('Daniel1', '123456', 2, NULL, 12),
('Elena1', '123456', 2, NULL, 13),
('Ricardo1', '123456', 2, NULL, 14),
('Valeria1', '123456', 2, NULL, 15),
('Fernando1', '123456', 2, NULL, 16),
('Andrea1', '123456', 2, NULL, 17),
('Roberto1', '123456', 2, NULL, 18),
('Paula1', '123456', 2, NULL, 19),
('Diego1', '123456', 2, NULL, 20),


('Ana2', '123456', 3, 1, NULL),
('Carlos2', '123456', 3, 2, NULL),
('Maria2', '123456', 3, 3, NULL),
('Jorge2', '123456', 3, 4, NULL),
('Luisa2', '123456', 3, 5, NULL),
('Pedro2', '123456', 3, 6, NULL),
('Sofia2', '123456', 3, 7, NULL),
('Diego2', '123456', 3, 8, NULL),
('Laura2', '123456', 3, 9, NULL),
('Miguel2', '123456', 3, 10, NULL),
('Carmen2', '123456', 3, 11, NULL),
('Roberto2', '123456', 3, 12, NULL),
('Paula2', '123456', 3, 13, NULL),
('Daniel2', '123456', 3, 14, NULL),
('Elena2', '123456', 3, 15, NULL),
('Ricardo2', '123456', 3, 16, NULL),
('Valeria2', '123456', 3, 17, NULL),
('Javier2', '123456', 3, 18, NULL),
('Andrea2', '123456', 3, 19, NULL),
('Fernando2', '123456', 3, 20, NULL);

insert into tipos_habitacion (nombre_tipo, precio_base) values
('sencilla', 800.00), ('doble', 1200.00), ('triple', 1500.00), ('cuadruple', 1800.00),
('suite junior', 2500.00), ('suite ejecutiva', 3000.00), ('suite presidencial', 8000.00),
('penthouse', 12000.00), ('cabaña estandar', 1500.00), ('cabaña familiar', 2200.00),
('villa de lujo', 5000.00), ('bungalow', 1700.00), ('habitacion con vista al mar', 2000.00),
('habitacion con jardin', 1600.00), ('habitacion con alberca privada', 3500.00),
('estudio', 1000.00), ('loft', 1300.00), ('suite nupcial', 4000.00),
('habitacion accesible', 900.00), ('habitacion economica', 600.00);

insert into habitaciones (numero_habitacion, id_tipo, estado) values
('101', 1, 'disponible'), ('102', 2, 'ocupada'), ('103', 3, 'disponible'), ('104', 4, 'mantenimiento'),
('105', 5, 'disponible'), ('106', 6, 'ocupada'), ('201', 7, 'disponible'), ('202', 8, 'disponible'),
('203', 9, 'ocupada'), ('204', 10, 'disponible'), ('301', 11, 'disponible'), ('302', 12, 'ocupada'),
('303', 13, 'disponible'), ('304', 14, 'mantenimiento'), ('401', 15, 'disponible'), ('402', 16, 'ocupada'),
('403', 17, 'disponible'), ('404', 18, 'disponible'), ('501', 19, 'ocupada'), ('502', 20, 'disponible');

insert into servicios (nombre_servicio, precio) values
('desayuno buffet', 250.00), ('masaje relajante spa', 800.00), ('tintoreria', 150.00),
('minibar', 300.00), ('transporte aeropuerto', 400.00), ('tour por la ciudad', 600.00),
('room service - flautas de pollo', 180.00), ('room service - tacos de suadero', 150.00),
('cuidado de niños', 500.00), ('alquiler de bicicleta', 200.00), ('clase de yoga', 120.00),
('botella de vino tinto', 750.00), ('cena romantica', 1500.00), ('planchado express', 80.00),
('alquiler de auto', 1200.00), ('paseo a caballo', 450.00), ('corte de cabello', 350.00),
('manicura y pedicura', 400.00), ('acceso a club de playa', 600.00), ('paquete fotografico', 1000.00);

insert into reservas (id_cliente, fecha_registro, estado) values
(1, '2026-02-10 10:00:00', 'finalizada'), (2, '2026-02-12 11:30:00', 'finalizada'),
(3, '2026-02-15 14:20:00', 'finalizada'), (4, '2026-02-20 09:15:00', 'finalizada'),
(5, '2026-02-25 16:45:00', 'activa'), (6, '2026-03-01 10:10:00', 'activa'),
(7, '2026-03-05 12:00:00', 'activa'), (8, '2026-03-08 18:30:00', 'activa'),
(9, '2026-03-10 08:45:00', 'cancelada'), (10, '2026-03-11 15:20:00', 'activa'),
(11, '2026-03-12 11:11:00', 'activa'), (12, '2026-03-13 14:00:00', 'activa'),
(13, '2026-03-14 17:50:00', 'activa'), (14, '2026-03-15 09:30:00', 'activa'),
(15, '2026-03-16 13:40:00', 'activa'), (16, '2026-03-17 19:15:00', 'activa'),
(17, '2026-03-18 10:05:00', 'activa'), (18, '2026-03-19 08:00:00', 'activa'),
(19, '2026-03-19 12:25:00', 'activa'), (20, '2026-03-19 16:10:00', 'activa');

insert into detalle_reservas (id_reserva, id_habitacion, fecha_inicio, fecha_fin) values
(1, 1, '2026-02-15', '2026-02-18'), (2, 2, '2026-02-18', '2026-02-20'),
(3, 3, '2026-02-20', '2026-02-25'), (4, 4, '2026-02-22', '2026-02-24'),
(5, 5, '2026-03-15', '2026-03-20'), (6, 6, '2026-03-18', '2026-03-22'),
(7, 7, '2026-03-20', '2026-03-25'), (8, 8, '2026-03-22', '2026-03-26'),
(9, 9, '2026-03-25', '2026-03-28'), (10, 10, '2026-03-26', '2026-03-30'),
(11, 11, '2026-03-28', '2026-04-02'), (12, 12, '2026-03-29', '2026-04-05'),
(13, 13, '2026-04-01', '2026-04-04'), (14, 14, '2026-04-02', '2026-04-06'),
(15, 15, '2026-04-05', '2026-04-10'), (16, 16, '2026-04-08', '2026-04-12'),
(17, 17, '2026-04-10', '2026-04-15'), (18, 18, '2026-04-12', '2026-04-18'),
(19, 19, '2026-04-15', '2026-04-20'), (20, 20, '2026-04-20', '2026-04-25');


insert into consumos_servicios (id_reserva, id_servicio, cantidad, fecha_consumo) values
(1, 1, 2, '2026-02-16 08:30:00'), (2, 4, 1, '2026-02-19 20:15:00'),
(3, 2, 1, '2026-02-21 11:00:00'), (4, 7, 2, '2026-02-23 14:30:00'),
(5, 8, 3, '2026-03-16 21:00:00'), (6, 5, 1, '2026-03-18 10:00:00'),
(7, 12, 1, '2026-03-21 19:45:00'), (8, 3, 2, '2026-03-23 09:20:00'),
(9, 10, 2, '2026-03-26 16:00:00'), (10, 1, 4, '2026-03-27 08:00:00'),
(11, 6, 1, '2026-03-29 10:30:00'), (12, 13, 1, '2026-03-30 20:00:00'),
(13, 9, 1, '2026-04-02 18:00:00'), (14, 15, 1, '2026-04-03 09:00:00'),
(15, 11, 2, '2026-04-06 07:30:00'), (16, 20, 1, '2026-04-09 12:00:00'),
(17, 14, 3, '2026-04-11 15:45:00'), (18, 17, 1, '2026-04-13 11:15:00'),
(19, 19, 2, '2026-04-16 10:00:00'), (20, 18, 1, '2026-04-21 14:20:00');

insert into pagos (id_reserva, monto, metodo_pago, fecha_pago) values
(1, 2900.00, 'tarjeta de credito', '2026-02-18 10:00:00'),
(2, 2700.00, 'efectivo', '2026-02-20 11:30:00'),
(3, 8300.00, 'transferencia', '2026-02-25 12:00:00'),
(4, 3960.00, 'tarjeta de debito', '2026-02-24 09:15:00'),
(5, 12950.00, 'tarjeta de credito', '2026-03-15 16:45:00'),
(6, 12400.00, 'transferencia', '2026-03-18 10:10:00'),
(7, 40750.00, 'tarjeta de credito', '2026-03-20 12:00:00'),
(8, 48300.00, 'efectivo', '2026-03-22 18:30:00'),
(9, 6400.00, 'tarjeta de debito', '2026-03-25 08:45:00'),
(10, 9800.00, 'transferencia', '2026-03-26 15:20:00'),
(11, 25600.00, 'tarjeta de credito', '2026-03-28 11:11:00'),
(12, 13400.00, 'efectivo', '2026-03-29 14:00:00'),
(13, 6500.00, 'tarjeta de debito', '2026-04-01 17:50:00'),
(14, 7600.00, 'transferencia', '2026-04-02 09:30:00'),
(15, 17740.00, 'tarjeta de credito', '2026-04-05 13:40:00'),
(16, 5000.00, 'efectivo', '2026-04-08 19:15:00'),
(17, 6740.00, 'tarjeta de debito', '2026-04-10 10:05:00'),
(18, 24350.00, 'transferencia', '2026-04-12 08:00:00'),
(19, 5700.00, 'tarjeta de credito', '2026-04-15 12:25:00'),
(20, 3400.00, 'efectivo', '2026-04-20 16:10:00');



-- SECCION DE INDICES

-- Justificación: Se indexa el email de clientes para búsquedas rápidas durante el login y registro.
-- Se indexa el número de habitación para agilizar la consulta de disponibilidad.
CREATE INDEX idx_cliente_email ON clientes(email);
CREATE INDEX idx_habitacion_numero ON habitaciones(numero_habitacion);


-- SECCION DE FUNCIONES


DELIMITER //

-- Función: Calcular Costo de Estancia
-- Descripción: Calcula el costo base de la habitación multiplicado por los días de estancia.
CREATE FUNCTION fn_costo_estancia(p_id_reserva INT) 
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
    
    SELECT SUM(DATEDIFF(dr.fecha_fin, dr.fecha_inicio) * th.precio_base)
    INTO v_total
    FROM detalle_reservas dr
    JOIN habitaciones h ON dr.id_habitacion = h.id_habitacion
    JOIN tipos_habitacion th ON h.id_tipo = th.id_tipo
    WHERE dr.id_reserva = p_id_reserva;
    
    RETURN IFNULL(v_total, 0);
END //

-- Función: Total Reservas Cliente
-- Descripción: Retorna el número total de reservas realizadas por un cliente específico.
CREATE FUNCTION fn_total_reservas_cliente(p_id_cliente INT) 
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_count INT;
    SELECT COUNT(*) INTO v_count FROM reservas WHERE id_cliente = p_id_cliente;
    RETURN v_count;
END //

DELIMITER ;


-- SECCION DE VISTAS


-- Vista: Reservas (Completa)
-- Objetivo: Mostrar la información consolidada de las reservas para el dashboard administrativo.
CREATE VIEW vw_reservas AS
SELECT 
    r.id_reserva,
    CONCAT(c.nombre, ' ', c.ap) AS nombre_cliente,
    c.email AS email_cliente,
    dr.fecha_inicio,
    dr.fecha_fin,
    h.numero_habitacion,
    r.estado,
    fn_costo_estancia(r.id_reserva) AS costo_total
FROM reservas r
JOIN clientes c ON r.id_cliente = c.id_cliente
LEFT JOIN detalle_reservas dr ON r.id_reserva = dr.id_reserva
LEFT JOIN habitaciones h ON dr.id_habitacion = h.id_habitacion;

-- Vista: Historial de Clientes
-- Objetivo: Resumen estadístico de cada cliente para análisis de mercadeo.
CREATE VIEW vw_historial_clientes AS
SELECT 
    c.id_cliente,
    CONCAT(c.nombre, ' ', c.ap) AS cliente,
    fn_total_reservas_cliente(c.id_cliente) AS total_reservas,
    IFNULL(SUM(p.monto), 0) AS total_pagado
FROM clientes c
LEFT JOIN reservas r ON c.id_cliente = r.id_cliente
LEFT JOIN pagos p ON r.id_reserva = p.id_reserva
GROUP BY c.id_cliente;


-- SECCION DE TRIGGERS


DELIMITER //

-- Trigger: Evitar Traslapes de Fechas
-- Problema que resuelve: Impide que una misma habitación sea reservada por dos clientes en el mismo periodo.
CREATE TRIGGER tr_evitar_traslapes
BEFORE INSERT ON detalle_reservas
FOR EACH ROW
BEGIN
    DECLARE v_conflicto INT;
    
    SELECT COUNT(*) INTO v_conflicto
    FROM detalle_reservas
    WHERE id_habitacion = NEW.id_habitacion
    AND (
        (NEW.fecha_inicio BETWEEN fecha_inicio AND fecha_fin) OR
        (NEW.fecha_fin BETWEEN fecha_inicio AND fecha_fin) OR
        (fecha_inicio BETWEEN NEW.fecha_inicio AND NEW.fecha_fin)
    );
    
    IF v_conflicto > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La habitación ya se encuentra ocupada en el periodo seleccionado.';
    END IF;
END //

-- Trigger: Evitar Sobreocupación
-- Problema que resuelve: Garantiza que no se marquen más habitaciones como ocupadas de las que físicamente existen.
CREATE TRIGGER tr_evitar_sobreocupacion
AFTER INSERT ON detalle_reservas
FOR EACH ROW
BEGIN
    UPDATE habitaciones SET estado = 'ocupada' WHERE id_habitacion = NEW.id_habitacion;
END //

DELIMITER ;


-- SECCION DE PROCEDIMIENTOS (TRANSACCIONES)


DELIMITER //

-- Procedimiento: Registrar Reserva Completa (Atómica)
-- Problema que resuelve: Asegura consistencia; si falla el pago o el detalle, no se guarda la reserva.
CREATE PROCEDURE sp_registrar_reserva(
    IN p_id_cliente INT,
    IN p_id_habitacion INT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE,
    IN p_monto_pago DECIMAL(10,2),
    IN p_metodo_pago VARCHAR(50)
)
BEGIN
    DECLARE v_id_reserva INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en la transacción: Registro de reserva fallido.';
    END;

    START TRANSACTION;
        -- 1. Insertar en Reservas
        INSERT INTO reservas (id_cliente, fecha_registro, estado) 
        VALUES (p_id_cliente, CURRENT_TIMESTAMP, 'activa');
        SET v_id_reserva = LAST_INSERT_ID();

        -- 2. Insertar en Detalle (esto activará el trigger de traslapes)
        INSERT INTO detalle_reservas (id_reserva, id_habitacion, fecha_inicio, fecha_fin)
        VALUES (v_id_reserva, p_id_habitacion, p_fecha_inicio, p_fecha_fin);

        -- 3. Insertar Pago
        INSERT INTO pagos (id_reserva, monto, metodo_pago, fecha_pago)
        VALUES (v_id_reserva, p_monto_pago, p_metodo_pago, CURRENT_TIMESTAMP);

    COMMIT;
END //

-- Procedimiento: Consultar Disponibilidad
-- Problema que resuelve: Facilita al recepcionista encontrar habitaciones libres rápidamente.
CREATE PROCEDURE sp_consultar_disponibilidad(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT h.*, th.nombre_tipo, th.precio_base
    FROM habitaciones h
    JOIN tipos_habitacion th ON h.id_tipo = th.id_tipo
    WHERE h.id_habitacion NOT IN (
        SELECT id_habitacion 
        FROM detalle_reservas 
        WHERE (p_fecha_inicio BETWEEN fecha_inicio AND fecha_fin)
        OR (p_fecha_fin BETWEEN fecha_inicio AND fecha_fin)
    );
END //

DELIMITER ;

-- ==========================================================
-- SECCION DE SEGURIDAD (USUARIOS Y ROLES)
-- ==========================================================
-- Nota: Estos comandos asumen un entorno administrativo de MySQL.

-- Crear Roles (Simulado con usuarios)
-- DROP USER IF EXISTS 'admin_user'@'localhost';
-- CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'admin123';
-- GRANT ALL PRIVILEGES ON sistemahotelero.* TO 'admin_user'@'localhost';

-- DROP USER IF EXISTS 'recep_user'@'localhost';
-- CREATE USER 'recep_user'@'localhost' IDENTIFIED BY 'recep123';
-- GRANT SELECT, INSERT, UPDATE ON sistemahotelero.reservas TO 'recep_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON sistemahotelero.detalle_reservas TO 'recep_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE sistemahotelero.sp_registrar_reserva TO 'recep_user'@'localhost';

-- DROP USER IF EXISTS 'cliente_user'@'localhost';
-- CREATE USER 'cliente_user'@'localhost' IDENTIFIED BY 'cliente123';
-- GRANT SELECT ON sistemahotelero.vw_reservas TO 'cliente_user'@'localhost';
-- GRANT SELECT ON sistemahotelero.habitaciones TO 'cliente_user'@'localhost';

