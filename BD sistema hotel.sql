create database sistemahotelero;
use sistemahotelero; 

create table clientes (
    id_cliente int auto_increment primary key,
    nombre varchar(100) not null,
    email varchar(100) unique not null,
    telefono varchar(15) not null
);

create table tipos_habitacion (
    id_tipo int auto_increment primary key,
    nombre_tipo varchar(50) not null,
    precio_base decimal(10,2) not null
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
    precio decimal(10,2) not null
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
    monto decimal(10,2) not null,
    fecha_pago datetime default current_timestamp,
    metodo_pago varchar(50) not null,
    foreign key (id_reserva) references reservas(id_reserva) on delete cascade
);