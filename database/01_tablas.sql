-- ============================================================
-- David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
-- Ambiente Web Cliente/Servidor · SC-502
-- Universidad Fidélitas · Costa Rica
-- ============================================================
--  VENTICA · 01_tablas.sql
--  Creación de la base de datos y de las 11 tablas del modelo
--  entidad-relación (normalizado a 3FN).
--
--  Orden de ejecución de los scripts:
--    1. 01_tablas.sql          (este archivo)
--    2. 02_procedimientos.sql
--    3. 03_datos_prueba.sql
--
--  Cómo correrlo: MySQL Workbench conectado a Aiven (o phpMyAdmin en XAMPP)
--  > abrir este archivo > ejecutar todo.
--  OJO: si las tablas ya existían, las borra y las vuelve a crear vacías.
--  En la base compartida de Aiven lo corre UNA sola persona.
-- ============================================================

CREATE DATABASE IF NOT EXISTS ventica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ventica;

-- Se borran en orden inverso para no chocar con las llaves foráneas
DROP TABLE IF EXISTS favorito;
DROP TABLE IF EXISTS resena;
DROP TABLE IF EXISTS direccion_entrega;
DROP TABLE IF EXISTS detalle_pedido;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS imagen_producto;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS emprendimiento;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS rol;


-- ------------------------------------------------------------
-- 1. ROL
--    Tipos de usuario: Cliente, Emprendedor, Administrador.
-- ------------------------------------------------------------
CREATE TABLE rol (
    id_rol      INT          NOT NULL AUTO_INCREMENT,
    nombre_rol  VARCHAR(30)  NOT NULL,

    CONSTRAINT pk_rol PRIMARY KEY (id_rol),
    CONSTRAINT uq_rol_nombre UNIQUE (nombre_rol)
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 2. USUARIO
--    Toda persona que entra a Ventica. Un rol tiene muchos usuarios.
--    La contraseña se guarda cifrada con password_hash() de PHP (60+ caracteres).
--    provincia y direccion: dirección de entrega predeterminada del cliente.
-- ------------------------------------------------------------
CREATE TABLE usuario (
    id_usuario      INT           NOT NULL AUTO_INCREMENT,
    id_rol          INT           NOT NULL,
    nombre          VARCHAR(100)  NOT NULL,
    correo          VARCHAR(150)  NOT NULL,
    contrasena      VARCHAR(255)  NOT NULL,
    telefono        VARCHAR(15)   NULL,
    provincia       ENUM('San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón') NULL,
    direccion       VARCHAR(255)  NULL,
    fecha_registro  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado          ENUM('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',

    CONSTRAINT pk_usuario PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuario_correo UNIQUE (correo),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol)
        REFERENCES rol (id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 3. EMPRENDIMIENTO
--    La "vitrina" de un emprendedor. Un usuario administra como máximo
--    un emprendimiento (relación 1 a 0..1, por eso id_usuario es UNIQUE).
--    El administrador lo aprueba o rechaza (estado_verificacion).
--    metodos_pago: los que acepta, separados por coma (sinpe,tarjeta,efectivo).
--    observacion_admin: motivo que escribe el admin al rechazar la solicitud.
-- ------------------------------------------------------------
CREATE TABLE emprendimiento (
    id_emprendimiento    INT           NOT NULL AUTO_INCREMENT,
    id_usuario           INT           NOT NULL,
    nombre_negocio       VARCHAR(120)  NOT NULL,
    descripcion          TEXT          NULL,
    ubicacion            VARCHAR(150)  NULL,
    logo                 VARCHAR(255)  NULL,
    metodos_pago         VARCHAR(40)   NOT NULL DEFAULT 'sinpe',
    observacion_admin    VARCHAR(500)  NULL,
    estado_verificacion  ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL DEFAULT 'pendiente',
    fecha_solicitud      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_emprendimiento PRIMARY KEY (id_emprendimiento),
    CONSTRAINT uq_emprendimiento_usuario UNIQUE (id_usuario),
    CONSTRAINT fk_emprendimiento_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 4. CATEGORIA
--    Crochet, Stickers, Velas, Belleza, Hogar, Moda, Papelería, Café...
--    El admin las gestiona desde "Gestión de Categorías".
--    artesanal = 1 si es de productos hechos a mano (filtro del admin).
-- ------------------------------------------------------------
CREATE TABLE categoria (
    id_categoria  INT           NOT NULL AUTO_INCREMENT,
    nombre        VARCHAR(60)   NOT NULL,
    icono         VARCHAR(40)   NULL,
    descripcion   VARCHAR(255)  NULL,
    artesanal     TINYINT(1)    NOT NULL DEFAULT 1,

    CONSTRAINT pk_categoria PRIMARY KEY (id_categoria),
    CONSTRAINT uq_categoria_nombre UNIQUE (nombre)
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 5. PRODUCTO
--    Lo que publica cada emprendimiento, dentro de una categoría.
--    precio en colones (DECIMAL para no perder exactitud).
-- ------------------------------------------------------------
CREATE TABLE producto (
    id_producto          INT            NOT NULL AUTO_INCREMENT,
    id_emprendimiento    INT            NOT NULL,
    id_categoria         INT            NOT NULL,
    nombre               VARCHAR(120)   NOT NULL,
    descripcion          TEXT           NULL,
    precio               DECIMAL(10, 2) NOT NULL,
    cantidad_disponible  INT            NOT NULL DEFAULT 0,
    estado               ENUM('activo', 'inactivo', 'agotado') NOT NULL DEFAULT 'activo',

    CONSTRAINT pk_producto PRIMARY KEY (id_producto),
    CONSTRAINT ck_producto_precio CHECK (precio >= 0),
    CONSTRAINT ck_producto_cantidad CHECK (cantidad_disponible >= 0),
    CONSTRAINT fk_producto_emprendimiento FOREIGN KEY (id_emprendimiento)
        REFERENCES emprendimiento (id_emprendimiento)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria)
        REFERENCES categoria (id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 6. IMAGENPRODUCTO
--    Un producto puede tener varias fotos; "orden" define cuál sale primero.
--    url = ruta del archivo dentro de public/static/img/uploads/
-- ------------------------------------------------------------
CREATE TABLE imagen_producto (
    id_imagen    INT           NOT NULL AUTO_INCREMENT,
    id_producto  INT           NOT NULL,
    url          VARCHAR(255)  NOT NULL,
    orden        INT           NOT NULL DEFAULT 1,

    CONSTRAINT pk_imagen_producto PRIMARY KEY (id_imagen),
    CONSTRAINT fk_imagen_producto FOREIGN KEY (id_producto)
        REFERENCES producto (id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 7. PEDIDO
--    Encabezado de una compra hecha por un cliente.
-- ------------------------------------------------------------
CREATE TABLE pedido (
    id_pedido     INT            NOT NULL AUTO_INCREMENT,
    id_usuario    INT            NOT NULL,
    fecha_pedido  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    metodo_pago   ENUM('sinpe', 'tarjeta', 'efectivo') NOT NULL,
    total         DECIMAL(10, 2) NOT NULL DEFAULT 0,

    CONSTRAINT pk_pedido PRIMARY KEY (id_pedido),
    CONSTRAINT ck_pedido_total CHECK (total >= 0),
    CONSTRAINT fk_pedido_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 8. DETALLEPEDIDO
--    Cada producto dentro de un pedido. Se guarda el precio_unitario
--    del momento de la compra (si el emprendedor cambia el precio
--    después, el pedido viejo no cambia).
--    estado_entrega va aquí porque cada emprendedor entrega lo suyo.
-- ------------------------------------------------------------
CREATE TABLE detalle_pedido (
    id_detalle       INT            NOT NULL AUTO_INCREMENT,
    id_pedido        INT            NOT NULL,
    id_producto      INT            NOT NULL,
    cantidad         INT            NOT NULL,
    precio_unitario  DECIMAL(10, 2) NOT NULL,
    subtotal         DECIMAL(10, 2) NOT NULL,
    estado_entrega   ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'rechazado') NOT NULL DEFAULT 'pendiente',

    CONSTRAINT pk_detalle_pedido PRIMARY KEY (id_detalle),
    CONSTRAINT ck_detalle_cantidad CHECK (cantidad > 0),
    CONSTRAINT fk_detalle_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedido (id_pedido)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto)
        REFERENCES producto (id_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 9. DIRECCIONENTREGA
--    A dónde se entrega cada pedido (relación 1 a 1 con Pedido,
--    por eso id_pedido es UNIQUE).
-- ------------------------------------------------------------
CREATE TABLE direccion_entrega (
    id_direccion      INT           NOT NULL AUTO_INCREMENT,
    id_pedido         INT           NOT NULL,
    nombre_receptor   VARCHAR(100)  NOT NULL,
    telefono          VARCHAR(15)   NOT NULL,
    provincia         ENUM('San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón') NOT NULL,
    canton            VARCHAR(60)   NOT NULL,
    distrito          VARCHAR(60)   NOT NULL,
    direccion_exacta  VARCHAR(255)  NOT NULL,

    CONSTRAINT pk_direccion_entrega PRIMARY KEY (id_direccion),
    CONSTRAINT uq_direccion_pedido UNIQUE (id_pedido),
    CONSTRAINT fk_direccion_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedido (id_pedido)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 10. RESENA
--     Calificación de 1 a 5 estrellas que un cliente le da a un producto.
--     Una persona solo puede reseñar una vez cada producto.
-- ------------------------------------------------------------
CREATE TABLE resena (
    id_resena    INT           NOT NULL AUTO_INCREMENT,
    id_usuario   INT           NOT NULL,
    id_producto  INT           NOT NULL,
    calificacion TINYINT       NOT NULL,
    comentario   VARCHAR(500)  NULL,
    fecha        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_resena PRIMARY KEY (id_resena),
    CONSTRAINT uq_resena_usuario_producto UNIQUE (id_usuario, id_producto),
    CONSTRAINT ck_resena_calificacion CHECK (calificacion BETWEEN 1 AND 5),
    CONSTRAINT fk_resena_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_resena_producto FOREIGN KEY (id_producto)
        REFERENCES producto (id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- 11. FAVORITO
--     Tabla intermedia (muchos a muchos) entre Usuario y Producto.
--     Su llave primaria es compuesta: el mismo producto no se puede
--     marcar dos veces como favorito por la misma persona.
-- ------------------------------------------------------------
CREATE TABLE favorito (
    id_usuario      INT       NOT NULL,
    id_producto     INT       NOT NULL,
    fecha_agregado  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_favorito PRIMARY KEY (id_usuario, id_producto),
    CONSTRAINT fk_favorito_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_favorito_producto FOREIGN KEY (id_producto)
        REFERENCES producto (id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ------------------------------------------------------------
-- Índices extra para las búsquedas más comunes del catálogo
-- ------------------------------------------------------------
CREATE INDEX idx_producto_nombre ON producto (nombre);
CREATE INDEX idx_producto_estado ON producto (estado);
CREATE INDEX idx_emprendimiento_estado ON emprendimiento (estado_verificacion);


-- ------------------------------------------------------------
-- Datos base que la aplicación necesita para funcionar
-- (los datos de prueba van en 03_datos_prueba.sql)
-- ------------------------------------------------------------
INSERT INTO rol (nombre_rol) VALUES
    ('Cliente'),
    ('Emprendedor'),
    ('Administrador');

INSERT INTO categoria (nombre, icono, descripcion, artesanal) VALUES
    ('Crochet & Tejido',     'tijeras',   'Amigurumi y prendas tejidas a mano', 1),
    ('Stickers & Pines',     'destellos', 'Ilustración y humor tico', 0),
    ('Velas & Aromas',       'llama',     'Cera de soya y difusores', 1),
    ('Belleza & Maquillaje', 'sonrisa',   'Skincare y cosmética natural', 0),
    ('Hogar & Deco',         'casa',      'Cerámica y decoración', 1),
    ('Moda & Accesorios',    'camisa',    'Joyería y tote bags', 1),
    ('Papelería & Arte',     'lapiz',     'Prints, libretas y agendas', 0),
    ('Café & Delicias',      'cafe',      'Granos, salsas y café de especialidad', 0);
