-- ============================================================
-- David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
-- Ambiente Web Cliente/Servidor · SC-502
-- Universidad Fidélitas · Costa Rica
-- ============================================================
--  VENTICA · 01_tablas.sql
--  Crea la base "ventica" con el modelo entidad-relación del Avance 1
--  (tablas FIDE_<NOMBRE>_TB, normalizado a 3FN), las vistas que usan los
--  procedimientos y los datos base: roles, estados, métodos de pago,
--  tipos de teléfono, categorías y la división territorial completa de
--  Costa Rica (7 provincias, 84 cantones y sus distritos).
--
--  Agregado al modelo del Avance 1 (lo pedía la aplicación):
--    * FIDE_EMPRENDIMIENTO_METODO_PAGO_TB  formas de pago que acepta cada emprendimiento
--    * FIDE_EMPRENDIMIENTO_TB.OBSERVACION  motivo que escribe el admin al rechazar
--    * FIDE_CATEGORIA_TB.ES_ARTESANAL      categoría de productos hechos a mano
--    * FIDE_PEDIDO_TB.TELEFONO_RECEPTOR    teléfono de quien recibe el pedido
--
--  Orden de ejecución:  01_tablas.sql > 02_procedimientos.sql > 03_datos_prueba.sql
--  (php database/instalar.php corre los tres).
--  OJO: borra las tablas y las crea vacías. En Aiven lo corre UNA sola persona.
-- ============================================================

CREATE DATABASE IF NOT EXISTS ventica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ventica;

-- Tablas del modelo anterior (por si la base ya existía)
DROP TABLE IF EXISTS favorito, resena, direccion_entrega, detalle_pedido, pedido, imagen_producto, producto, categoria, emprendimiento, usuario, rol;

-- Vistas
DROP VIEW IF EXISTS FIDE_PRODUCTO_V, FIDE_EMPRENDIMIENTO_V, FIDE_USUARIO_V, FIDE_DIRECCION_V, FIDE_UBICACION_V;

-- Tablas en orden inverso a sus llaves foráneas
DROP TABLE IF EXISTS FIDE_CARRITO_DETALLE_TB;
DROP TABLE IF EXISTS FIDE_CARRITO_TB;
DROP TABLE IF EXISTS FIDE_FAVORITO_TB;
DROP TABLE IF EXISTS FIDE_RESENA_TB;
DROP TABLE IF EXISTS FIDE_DETALLE_PEDIDO_TB;
DROP TABLE IF EXISTS FIDE_PEDIDO_TB;
DROP TABLE IF EXISTS FIDE_IMAGEN_PRODUCTO_TB;
DROP TABLE IF EXISTS FIDE_PRODUCTO_TB;
DROP TABLE IF EXISTS FIDE_RED_SOCIAL_TB;
DROP TABLE IF EXISTS FIDE_EMPRENDIMIENTO_METODO_PAGO_TB;
DROP TABLE IF EXISTS FIDE_TELEFONO_TB;
DROP TABLE IF EXISTS FIDE_CORREO_TB;
DROP TABLE IF EXISTS FIDE_EMPRENDIMIENTO_TB;
DROP TABLE IF EXISTS FIDE_USUARIO_DIRECCION_TB;
DROP TABLE IF EXISTS FIDE_USUARIO_TB;
DROP TABLE IF EXISTS FIDE_DIRECCION_TB;
DROP TABLE IF EXISTS FIDE_DISTRITO_TB;
DROP TABLE IF EXISTS FIDE_CANTON_TB;
DROP TABLE IF EXISTS FIDE_PROVINCIA_TB;
DROP TABLE IF EXISTS FIDE_METODO_PAGO_TB;
DROP TABLE IF EXISTS FIDE_CATEGORIA_TB;
DROP TABLE IF EXISTS FIDE_TIPO_TELEFONO_TB;
DROP TABLE IF EXISTS FIDE_ROL_TB;
DROP TABLE IF EXISTS FIDE_ESTADO_TB;


-- ============================================================
-- CATÁLOGOS GENERALES
-- ============================================================

-- Estados de usuarios, emprendimientos, productos, pedidos y líneas de pedido
CREATE TABLE FIDE_ESTADO_TB (
    ID_ESTADO      INT           NOT NULL AUTO_INCREMENT,
    NOMBRE_ESTADO  VARCHAR(20)   NOT NULL,
    DESCRIPCION    VARCHAR(150)  NULL,

    CONSTRAINT PK_ESTADO PRIMARY KEY (ID_ESTADO),
    CONSTRAINT UQ_ESTADO_NOMBRE UNIQUE (NOMBRE_ESTADO)
) ENGINE = InnoDB;

-- Cliente, Emprendedor, Administrador
CREATE TABLE FIDE_ROL_TB (
    ID_ROL       INT           NOT NULL AUTO_INCREMENT,
    NOMBRE_ROL   VARCHAR(30)   NOT NULL,
    DESCRIPCION  VARCHAR(150)  NULL,

    CONSTRAINT PK_ROL PRIMARY KEY (ID_ROL),
    CONSTRAINT UQ_ROL_NOMBRE UNIQUE (NOMBRE_ROL)
) ENGINE = InnoDB;

-- Celular, Casa, Trabajo
CREATE TABLE FIDE_TIPO_TELEFONO_TB (
    ID_TIPO_TELEFONO  INT          NOT NULL AUTO_INCREMENT,
    NOMBRE_TIPO       VARCHAR(30)  NOT NULL,

    CONSTRAINT PK_TIPO_TELEFONO PRIMARY KEY (ID_TIPO_TELEFONO),
    CONSTRAINT UQ_TIPO_TELEFONO_NOMBRE UNIQUE (NOMBRE_TIPO)
) ENGINE = InnoDB;

-- NOMBRE_METODO es el código que usa la aplicación: sinpe, tarjeta, efectivo
CREATE TABLE FIDE_METODO_PAGO_TB (
    ID_METODO_PAGO  INT           NOT NULL AUTO_INCREMENT,
    NOMBRE_METODO   VARCHAR(20)   NOT NULL,
    DESCRIPCION     VARCHAR(150)  NULL,

    CONSTRAINT PK_METODO_PAGO PRIMARY KEY (ID_METODO_PAGO),
    CONSTRAINT UQ_METODO_PAGO_NOMBRE UNIQUE (NOMBRE_METODO)
) ENGINE = InnoDB;


-- ============================================================
-- UBICACIÓN (división territorial de Costa Rica)
-- Los id son los códigos oficiales: provincia 1, cantón 101, distrito 10101.
-- El código del distrito es también el código postal.
-- ============================================================

CREATE TABLE FIDE_PROVINCIA_TB (
    ID_PROVINCIA      INT          NOT NULL,
    NOMBRE_PROVINCIA  VARCHAR(20)  NOT NULL,

    CONSTRAINT PK_PROVINCIA PRIMARY KEY (ID_PROVINCIA),
    CONSTRAINT UQ_PROVINCIA_NOMBRE UNIQUE (NOMBRE_PROVINCIA)
) ENGINE = InnoDB;

CREATE TABLE FIDE_CANTON_TB (
    ID_CANTON      INT          NOT NULL,
    NOMBRE_CANTON  VARCHAR(40)  NOT NULL,
    ID_PROVINCIA   INT          NOT NULL,

    CONSTRAINT PK_CANTON PRIMARY KEY (ID_CANTON),
    CONSTRAINT FK_CANTON_PROVINCIA FOREIGN KEY (ID_PROVINCIA)
        REFERENCES FIDE_PROVINCIA_TB (ID_PROVINCIA)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE FIDE_DISTRITO_TB (
    ID_DISTRITO      INT          NOT NULL,
    NOMBRE_DISTRITO  VARCHAR(40)  NOT NULL,
    ID_CANTON        INT          NOT NULL,

    CONSTRAINT PK_DISTRITO PRIMARY KEY (ID_DISTRITO),
    CONSTRAINT FK_DISTRITO_CANTON FOREIGN KEY (ID_CANTON)
        REFERENCES FIDE_CANTON_TB (ID_CANTON)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Una dirección exacta: distrito + otras señas. La usan usuarios, emprendimientos y pedidos.
CREATE TABLE FIDE_DIRECCION_TB (
    ID_DIRECCION   INT           NOT NULL AUTO_INCREMENT,
    ID_DISTRITO    INT           NOT NULL,
    OTRAS_SENAS    VARCHAR(255)  NULL,
    CODIGO_POSTAL  VARCHAR(5)    NULL,

    CONSTRAINT PK_DIRECCION PRIMARY KEY (ID_DIRECCION),
    CONSTRAINT FK_DIRECCION_DISTRITO FOREIGN KEY (ID_DISTRITO)
        REFERENCES FIDE_DISTRITO_TB (ID_DISTRITO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ============================================================
-- IDENTIDAD Y CONTACTO
-- ============================================================

-- Toda persona que entra a Ventica. El correo y el teléfono van en sus propias tablas.
-- CONTRASENA_HASH: password_hash() de PHP.
CREATE TABLE FIDE_USUARIO_TB (
    ID_USUARIO        INT           NOT NULL AUTO_INCREMENT,
    CEDULA            VARCHAR(12)   NOT NULL,
    NOMBRE            VARCHAR(60)   NOT NULL,
    PRIMER_APELLIDO   VARCHAR(40)   NOT NULL,
    SEGUNDO_APELLIDO  VARCHAR(40)   NULL,
    CONTRASENA_HASH   VARCHAR(255)  NOT NULL,
    FECHA_REGISTRO    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ID_ROL            INT           NOT NULL,
    ID_ESTADO         INT           NOT NULL,

    CONSTRAINT PK_USUARIO PRIMARY KEY (ID_USUARIO),
    CONSTRAINT UQ_USUARIO_CEDULA UNIQUE (CEDULA),
    CONSTRAINT FK_USUARIO_ROL FOREIGN KEY (ID_ROL)
        REFERENCES FIDE_ROL_TB (ID_ROL)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_USUARIO_ESTADO FOREIGN KEY (ID_ESTADO)
        REFERENCES FIDE_ESTADO_TB (ID_ESTADO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Direcciones guardadas de cada usuario (muchos a muchos). ES_PRINCIPAL = la que se
-- ofrece al pagar. Si la persona cambia su dirección se crea una nueva, así los
-- pedidos viejos conservan la suya.
CREATE TABLE FIDE_USUARIO_DIRECCION_TB (
    ID_USUARIO    INT          NOT NULL,
    ID_DIRECCION  INT          NOT NULL,
    ALIAS         VARCHAR(40)  NULL,
    ES_PRINCIPAL  TINYINT(1)   NOT NULL DEFAULT 0,

    CONSTRAINT PK_USUARIO_DIRECCION PRIMARY KEY (ID_USUARIO, ID_DIRECCION),
    CONSTRAINT FK_USUARIO_DIRECCION_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_USUARIO_DIRECCION_DIRECCION FOREIGN KEY (ID_DIRECCION)
        REFERENCES FIDE_DIRECCION_TB (ID_DIRECCION)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- La vitrina de un emprendedor. Un usuario tiene como máximo un emprendimiento.
-- ID_ESTADO: pendiente, aprobado o rechazado (lo decide el admin).
CREATE TABLE FIDE_EMPRENDIMIENTO_TB (
    ID_EMPRENDIMIENTO   INT           NOT NULL AUTO_INCREMENT,
    ID_USUARIO          INT           NOT NULL,
    NOMBRE_NEGOCIO      VARCHAR(120)  NOT NULL,
    DESCRIPCION         TEXT          NULL,
    LOGO_URL            VARCHAR(255)  NULL,
    ID_DIRECCION        INT           NULL,
    FECHA_SOLICITUD     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FECHA_VERIFICACION  DATETIME      NULL,
    OBSERVACION         VARCHAR(500)  NULL,
    ID_ESTADO           INT           NOT NULL,

    CONSTRAINT PK_EMPRENDIMIENTO PRIMARY KEY (ID_EMPRENDIMIENTO),
    CONSTRAINT UQ_EMPRENDIMIENTO_USUARIO UNIQUE (ID_USUARIO),
    CONSTRAINT FK_EMPRENDIMIENTO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_EMPRENDIMIENTO_DIRECCION FOREIGN KEY (ID_DIRECCION)
        REFERENCES FIDE_DIRECCION_TB (ID_DIRECCION)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT FK_EMPRENDIMIENTO_ESTADO FOREIGN KEY (ID_ESTADO)
        REFERENCES FIDE_ESTADO_TB (ID_ESTADO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Correos de un usuario o de un emprendimiento (uno de los dos). El correo es único: con él se inicia sesión.
CREATE TABLE FIDE_CORREO_TB (
    ID_CORREO          INT           NOT NULL AUTO_INCREMENT,
    CORREO             VARCHAR(150)  NOT NULL,
    ID_USUARIO         INT           NULL,
    ID_EMPRENDIMIENTO  INT           NULL,

    CONSTRAINT PK_CORREO PRIMARY KEY (ID_CORREO),
    CONSTRAINT UQ_CORREO UNIQUE (CORREO),
    CONSTRAINT FK_CORREO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_CORREO_EMPRENDIMIENTO FOREIGN KEY (ID_EMPRENDIMIENTO)
        REFERENCES FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- Teléfonos de un usuario o de un emprendimiento
CREATE TABLE FIDE_TELEFONO_TB (
    ID_TELEFONO        INT          NOT NULL AUTO_INCREMENT,
    NUMERO             VARCHAR(15)  NOT NULL,
    ID_TIPO_TELEFONO   INT          NOT NULL,
    ID_USUARIO         INT          NULL,
    ID_EMPRENDIMIENTO  INT          NULL,

    CONSTRAINT PK_TELEFONO PRIMARY KEY (ID_TELEFONO),
    CONSTRAINT FK_TELEFONO_TIPO FOREIGN KEY (ID_TIPO_TELEFONO)
        REFERENCES FIDE_TIPO_TELEFONO_TB (ID_TIPO_TELEFONO)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_TELEFONO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_TELEFONO_EMPRENDIMIENTO FOREIGN KEY (ID_EMPRENDIMIENTO)
        REFERENCES FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- Redes sociales de cada emprendimiento (Instagram, Facebook, TikTok...)
CREATE TABLE FIDE_RED_SOCIAL_TB (
    ID_RED_SOCIAL      INT           NOT NULL AUTO_INCREMENT,
    ID_EMPRENDIMIENTO  INT           NOT NULL,
    PLATAFORMA         VARCHAR(30)   NOT NULL,
    URL_PERFIL         VARCHAR(255)  NOT NULL,

    CONSTRAINT PK_RED_SOCIAL PRIMARY KEY (ID_RED_SOCIAL),
    CONSTRAINT FK_RED_SOCIAL_EMPRENDIMIENTO FOREIGN KEY (ID_EMPRENDIMIENTO)
        REFERENCES FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- (Agregada) Formas de pago que acepta cada emprendimiento. Muchos a muchos.
CREATE TABLE FIDE_EMPRENDIMIENTO_METODO_PAGO_TB (
    ID_EMPRENDIMIENTO  INT  NOT NULL,
    ID_METODO_PAGO     INT  NOT NULL,

    CONSTRAINT PK_EMPRENDIMIENTO_METODO_PAGO PRIMARY KEY (ID_EMPRENDIMIENTO, ID_METODO_PAGO),
    CONSTRAINT FK_EMP_METODO_EMPRENDIMIENTO FOREIGN KEY (ID_EMPRENDIMIENTO)
        REFERENCES FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_EMP_METODO_METODO FOREIGN KEY (ID_METODO_PAGO)
        REFERENCES FIDE_METODO_PAGO_TB (ID_METODO_PAGO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- ============================================================
-- CATÁLOGO E INTERACCIÓN
-- ============================================================

CREATE TABLE FIDE_CATEGORIA_TB (
    ID_CATEGORIA      INT           NOT NULL AUTO_INCREMENT,
    NOMBRE_CATEGORIA  VARCHAR(60)   NOT NULL,
    ICONO             VARCHAR(40)   NULL,
    DESCRIPCION       VARCHAR(255)  NULL,
    ES_ARTESANAL      TINYINT(1)    NOT NULL DEFAULT 1,

    CONSTRAINT PK_CATEGORIA PRIMARY KEY (ID_CATEGORIA),
    CONSTRAINT UQ_CATEGORIA_NOMBRE UNIQUE (NOMBRE_CATEGORIA)
) ENGINE = InnoDB;

-- ID_ESTADO: activo, inactivo (pausado) o agotado
CREATE TABLE FIDE_PRODUCTO_TB (
    ID_PRODUCTO          INT             NOT NULL AUTO_INCREMENT,
    ID_EMPRENDIMIENTO    INT             NOT NULL,
    ID_CATEGORIA         INT             NOT NULL,
    NOMBRE_PRODUCTO      VARCHAR(120)    NOT NULL,
    DESCRIPCION          TEXT            NULL,
    PRECIO               DECIMAL(10, 2)  NOT NULL,
    CANTIDAD_DISPONIBLE  INT             NOT NULL DEFAULT 0,
    FECHA_PUBLICACION    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ID_ESTADO            INT             NOT NULL,

    CONSTRAINT PK_PRODUCTO PRIMARY KEY (ID_PRODUCTO),
    CONSTRAINT CK_PRODUCTO_PRECIO CHECK (PRECIO >= 0),
    CONSTRAINT CK_PRODUCTO_CANTIDAD CHECK (CANTIDAD_DISPONIBLE >= 0),
    CONSTRAINT FK_PRODUCTO_EMPRENDIMIENTO FOREIGN KEY (ID_EMPRENDIMIENTO)
        REFERENCES FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_PRODUCTO_CATEGORIA FOREIGN KEY (ID_CATEGORIA)
        REFERENCES FIDE_CATEGORIA_TB (ID_CATEGORIA)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_PRODUCTO_ESTADO FOREIGN KEY (ID_ESTADO)
        REFERENCES FIDE_ESTADO_TB (ID_ESTADO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Fotos de un producto; ORDEN = 1 es la principal
CREATE TABLE FIDE_IMAGEN_PRODUCTO_TB (
    ID_IMAGEN    INT           NOT NULL AUTO_INCREMENT,
    ID_PRODUCTO  INT           NOT NULL,
    URL_IMAGEN   VARCHAR(255)  NOT NULL,
    ORDEN        INT           NOT NULL DEFAULT 1,

    CONSTRAINT PK_IMAGEN_PRODUCTO PRIMARY KEY (ID_IMAGEN),
    CONSTRAINT FK_IMAGEN_PRODUCTO FOREIGN KEY (ID_PRODUCTO)
        REFERENCES FIDE_PRODUCTO_TB (ID_PRODUCTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- Calificación de 1 a 5 estrellas. Una reseña por persona y producto.
CREATE TABLE FIDE_RESENA_TB (
    ID_RESENA     INT           NOT NULL AUTO_INCREMENT,
    ID_USUARIO    INT           NOT NULL,
    ID_PRODUCTO   INT           NOT NULL,
    CALIFICACION  TINYINT       NOT NULL,
    COMENTARIO    VARCHAR(500)  NULL,
    FECHA_RESENA  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PK_RESENA PRIMARY KEY (ID_RESENA),
    CONSTRAINT UQ_RESENA_USUARIO_PRODUCTO UNIQUE (ID_USUARIO, ID_PRODUCTO),
    CONSTRAINT CK_RESENA_CALIFICACION CHECK (CALIFICACION BETWEEN 1 AND 5),
    CONSTRAINT FK_RESENA_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_RESENA_PRODUCTO FOREIGN KEY (ID_PRODUCTO)
        REFERENCES FIDE_PRODUCTO_TB (ID_PRODUCTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- Muchos a muchos entre usuario y producto (llave compuesta)
CREATE TABLE FIDE_FAVORITO_TB (
    ID_USUARIO      INT       NOT NULL,
    ID_PRODUCTO     INT       NOT NULL,
    FECHA_AGREGADO  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PK_FAVORITO PRIMARY KEY (ID_USUARIO, ID_PRODUCTO),
    CONSTRAINT FK_FAVORITO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_FAVORITO_PRODUCTO FOREIGN KEY (ID_PRODUCTO)
        REFERENCES FIDE_PRODUCTO_TB (ID_PRODUCTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- ============================================================
-- CARRITO Y PEDIDOS
-- ============================================================

-- Un carrito por usuario con sesión (así lo encuentra en otro dispositivo)
CREATE TABLE FIDE_CARRITO_TB (
    ID_CARRITO           INT       NOT NULL AUTO_INCREMENT,
    ID_USUARIO           INT       NOT NULL,
    FECHA_CREACION       DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FECHA_ACTUALIZACION  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT PK_CARRITO PRIMARY KEY (ID_CARRITO),
    CONSTRAINT UQ_CARRITO_USUARIO UNIQUE (ID_USUARIO),
    CONSTRAINT FK_CARRITO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE FIDE_CARRITO_DETALLE_TB (
    ID_CARRITO      INT       NOT NULL,
    ID_PRODUCTO     INT       NOT NULL,
    CANTIDAD        INT       NOT NULL,
    FECHA_AGREGADO  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PK_CARRITO_DETALLE PRIMARY KEY (ID_CARRITO, ID_PRODUCTO),
    CONSTRAINT CK_CARRITO_CANTIDAD CHECK (CANTIDAD > 0),
    CONSTRAINT FK_CARRITO_DETALLE_CARRITO FOREIGN KEY (ID_CARRITO)
        REFERENCES FIDE_CARRITO_TB (ID_CARRITO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_CARRITO_DETALLE_PRODUCTO FOREIGN KEY (ID_PRODUCTO)
        REFERENCES FIDE_PRODUCTO_TB (ID_PRODUCTO)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- Encabezado de una compra. ID_DIRECCION: a dónde se entrega.
CREATE TABLE FIDE_PEDIDO_TB (
    ID_PEDIDO          INT             NOT NULL AUTO_INCREMENT,
    ID_USUARIO         INT             NOT NULL,
    ID_DIRECCION       INT             NOT NULL,
    ID_METODO_PAGO     INT             NOT NULL,
    FECHA_PEDIDO       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    NOMBRE_RECEPTOR    VARCHAR(100)    NOT NULL,
    TELEFONO_RECEPTOR  VARCHAR(15)     NOT NULL,
    TOTAL              DECIMAL(10, 2)  NOT NULL DEFAULT 0,
    ID_ESTADO          INT             NOT NULL,

    CONSTRAINT PK_PEDIDO PRIMARY KEY (ID_PEDIDO),
    CONSTRAINT CK_PEDIDO_TOTAL CHECK (TOTAL >= 0),
    CONSTRAINT FK_PEDIDO_USUARIO FOREIGN KEY (ID_USUARIO)
        REFERENCES FIDE_USUARIO_TB (ID_USUARIO)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_PEDIDO_DIRECCION FOREIGN KEY (ID_DIRECCION)
        REFERENCES FIDE_DIRECCION_TB (ID_DIRECCION)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_PEDIDO_METODO_PAGO FOREIGN KEY (ID_METODO_PAGO)
        REFERENCES FIDE_METODO_PAGO_TB (ID_METODO_PAGO)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_PEDIDO_ESTADO FOREIGN KEY (ID_ESTADO)
        REFERENCES FIDE_ESTADO_TB (ID_ESTADO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Cada producto del pedido con el precio del momento de la compra.
-- ID_ESTADO por línea: cada emprendedor confirma y entrega lo suyo.
CREATE TABLE FIDE_DETALLE_PEDIDO_TB (
    ID_DETALLE       INT             NOT NULL AUTO_INCREMENT,
    ID_PEDIDO        INT             NOT NULL,
    ID_PRODUCTO      INT             NOT NULL,
    CANTIDAD         INT             NOT NULL,
    PRECIO_UNITARIO  DECIMAL(10, 2)  NOT NULL,
    SUBTOTAL         DECIMAL(10, 2)  NOT NULL,
    ID_ESTADO        INT             NOT NULL,

    CONSTRAINT PK_DETALLE_PEDIDO PRIMARY KEY (ID_DETALLE),
    CONSTRAINT CK_DETALLE_CANTIDAD CHECK (CANTIDAD > 0),
    CONSTRAINT FK_DETALLE_PEDIDO FOREIGN KEY (ID_PEDIDO)
        REFERENCES FIDE_PEDIDO_TB (ID_PEDIDO)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_DETALLE_PRODUCTO FOREIGN KEY (ID_PRODUCTO)
        REFERENCES FIDE_PRODUCTO_TB (ID_PRODUCTO)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_DETALLE_ESTADO FOREIGN KEY (ID_ESTADO)
        REFERENCES FIDE_ESTADO_TB (ID_ESTADO)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;


-- Índices para las búsquedas más comunes
CREATE INDEX IDX_PRODUCTO_NOMBRE ON FIDE_PRODUCTO_TB (NOMBRE_PRODUCTO);
CREATE INDEX IDX_CORREO_USUARIO ON FIDE_CORREO_TB (ID_USUARIO);
CREATE INDEX IDX_TELEFONO_USUARIO ON FIDE_TELEFONO_TB (ID_USUARIO);


-- ============================================================
-- VISTAS
-- Juntan los datos que se repiten en muchos procedimientos.
-- Las columnas salen en minúscula con los nombres que usa PHP.
-- ============================================================

-- Distrito con su cantón y provincia
CREATE VIEW FIDE_UBICACION_V AS
SELECT d.ID_DISTRITO AS id_distrito, d.NOMBRE_DISTRITO AS distrito,
       c.ID_CANTON AS id_canton, c.NOMBRE_CANTON AS canton,
       p.ID_PROVINCIA AS id_provincia, p.NOMBRE_PROVINCIA AS provincia
FROM FIDE_DISTRITO_TB d
INNER JOIN FIDE_CANTON_TB c ON c.ID_CANTON = d.ID_CANTON
INNER JOIN FIDE_PROVINCIA_TB p ON p.ID_PROVINCIA = c.ID_PROVINCIA;

-- Dirección completa
CREATE VIEW FIDE_DIRECCION_V AS
SELECT dr.ID_DIRECCION AS id_direccion, dr.OTRAS_SENAS AS otras_senas, dr.CODIGO_POSTAL AS codigo_postal,
       ub.id_distrito, ub.distrito, ub.id_canton, ub.canton, ub.id_provincia, ub.provincia
FROM FIDE_DIRECCION_TB dr
INNER JOIN FIDE_UBICACION_V ub ON ub.id_distrito = dr.ID_DISTRITO;

-- Usuario con rol, estado, correo, teléfono y dirección principal.
-- nombre = nombre completo (nombre + apellidos)
CREATE VIEW FIDE_USUARIO_V AS
SELECT u.ID_USUARIO AS id_usuario, u.CEDULA AS cedula,
       CONCAT_WS(' ', u.NOMBRE, u.PRIMER_APELLIDO, u.SEGUNDO_APELLIDO) AS nombre,
       u.NOMBRE AS nombre_pila, u.PRIMER_APELLIDO AS primer_apellido, u.SEGUNDO_APELLIDO AS segundo_apellido,
       u.CONTRASENA_HASH AS contrasena, u.FECHA_REGISTRO AS fecha_registro,
       u.ID_ROL AS id_rol, r.NOMBRE_ROL AS nombre_rol, es.NOMBRE_ESTADO AS estado,
       (SELECT c.CORREO FROM FIDE_CORREO_TB c
         WHERE c.ID_USUARIO = u.ID_USUARIO ORDER BY c.ID_CORREO LIMIT 1) AS correo,
       (SELECT t.NUMERO FROM FIDE_TELEFONO_TB t
         WHERE t.ID_USUARIO = u.ID_USUARIO ORDER BY t.ID_TELEFONO LIMIT 1) AS telefono,
       dv.id_direccion, dv.otras_senas AS direccion,
       dv.id_distrito, dv.distrito, dv.id_canton, dv.canton, dv.id_provincia, dv.provincia
FROM FIDE_USUARIO_TB u
INNER JOIN FIDE_ROL_TB r ON r.ID_ROL = u.ID_ROL
INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = u.ID_ESTADO
LEFT JOIN FIDE_USUARIO_DIRECCION_TB ud ON ud.ID_USUARIO = u.ID_USUARIO AND ud.ES_PRINCIPAL = 1
LEFT JOIN FIDE_DIRECCION_V dv ON dv.id_direccion = ud.ID_DIRECCION;

-- Emprendimiento con estado, ubicación ("Cantón, Provincia") y métodos de pago ("sinpe,tarjeta")
CREATE VIEW FIDE_EMPRENDIMIENTO_V AS
SELECT e.ID_EMPRENDIMIENTO AS id_emprendimiento, e.ID_USUARIO AS id_usuario,
       e.NOMBRE_NEGOCIO AS nombre_negocio, e.DESCRIPCION AS descripcion, e.LOGO_URL AS logo,
       e.OBSERVACION AS observacion_admin, es.NOMBRE_ESTADO AS estado_verificacion,
       e.FECHA_SOLICITUD AS fecha_solicitud, e.FECHA_VERIFICACION AS fecha_verificacion,
       dv.id_direccion, dv.otras_senas, dv.id_distrito, dv.distrito, dv.id_canton, dv.canton,
       dv.id_provincia, dv.provincia,
       CONCAT(dv.canton, ', ', dv.provincia) AS ubicacion,
       COALESCE((SELECT GROUP_CONCAT(m.NOMBRE_METODO ORDER BY m.ID_METODO_PAGO SEPARATOR ',')
                 FROM FIDE_EMPRENDIMIENTO_METODO_PAGO_TB em
                 INNER JOIN FIDE_METODO_PAGO_TB m ON m.ID_METODO_PAGO = em.ID_METODO_PAGO
                 WHERE em.ID_EMPRENDIMIENTO = e.ID_EMPRENDIMIENTO), '') AS metodos_pago
FROM FIDE_EMPRENDIMIENTO_TB e
INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = e.ID_ESTADO
LEFT JOIN FIDE_DIRECCION_V dv ON dv.id_direccion = e.ID_DIRECCION;

-- Producto con estado, categoría, foto principal y calificación promedio
CREATE VIEW FIDE_PRODUCTO_V AS
SELECT p.ID_PRODUCTO AS id_producto, p.ID_EMPRENDIMIENTO AS id_emprendimiento, p.ID_CATEGORIA AS id_categoria,
       p.NOMBRE_PRODUCTO AS nombre, p.DESCRIPCION AS descripcion, p.PRECIO AS precio,
       p.CANTIDAD_DISPONIBLE AS cantidad_disponible, p.FECHA_PUBLICACION AS fecha_publicacion,
       es.NOMBRE_ESTADO AS estado, c.NOMBRE_CATEGORIA AS categoria,
       (SELECT i.URL_IMAGEN FROM FIDE_IMAGEN_PRODUCTO_TB i
         WHERE i.ID_PRODUCTO = p.ID_PRODUCTO ORDER BY i.ORDEN LIMIT 1) AS imagen,
       (SELECT ROUND(AVG(r.CALIFICACION), 1) FROM FIDE_RESENA_TB r
         WHERE r.ID_PRODUCTO = p.ID_PRODUCTO) AS calificacion
FROM FIDE_PRODUCTO_TB p
INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = p.ID_ESTADO
INNER JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA;


-- ============================================================
-- DATOS BASE (la aplicación los necesita para funcionar)
-- ============================================================

INSERT INTO FIDE_ESTADO_TB (ID_ESTADO, NOMBRE_ESTADO, DESCRIPCION) VALUES
    (1,  'activo',     'Cuenta o producto disponible'),
    (2,  'inactivo',   'Producto pausado por el emprendedor'),
    (3,  'bloqueado',  'Cuenta suspendida por el administrador'),
    (4,  'pendiente',  'Solicitud o pedido esperando respuesta'),
    (5,  'aprobado',   'Emprendimiento verificado por el administrador'),
    (6,  'rechazado',  'Solicitud o línea de pedido rechazada'),
    (7,  'agotado',    'Producto sin unidades disponibles'),
    (8,  'confirmado', 'El emprendedor confirmó el pago'),
    (9,  'enviado',    'El pedido va en camino'),
    (10, 'entregado',  'El cliente recibió el pedido');

INSERT INTO FIDE_ROL_TB (ID_ROL, NOMBRE_ROL, DESCRIPCION) VALUES
    (1, 'Cliente',       'Compra en el catálogo'),
    (2, 'Emprendedor',   'Publica y vende sus productos'),
    (3, 'Administrador', 'Aprueba emprendimientos y gestiona la plataforma');

INSERT INTO FIDE_TIPO_TELEFONO_TB (ID_TIPO_TELEFONO, NOMBRE_TIPO) VALUES
    (1, 'Celular'),
    (2, 'Casa'),
    (3, 'Trabajo');

INSERT INTO FIDE_METODO_PAGO_TB (ID_METODO_PAGO, NOMBRE_METODO, DESCRIPCION) VALUES
    (1, 'sinpe',    'SINPE Móvil'),
    (2, 'tarjeta',  'Tarjeta de crédito o débito'),
    (3, 'efectivo', 'Efectivo contra entrega');

INSERT INTO FIDE_CATEGORIA_TB (NOMBRE_CATEGORIA, ICONO, DESCRIPCION, ES_ARTESANAL) VALUES
    ('Crochet & Tejido',     'tijeras',   'Amigurumi y prendas tejidas a mano', 1),
    ('Stickers & Pines',     'destellos', 'Ilustración y humor tico', 0),
    ('Velas & Aromas',       'llama',     'Cera de soya y difusores', 1),
    ('Belleza & Maquillaje', 'sonrisa',   'Skincare y cosmética natural', 0),
    ('Hogar & Deco',         'casa',      'Cerámica y decoración', 1),
    ('Moda & Accesorios',    'camisa',    'Joyería y tote bags', 1),
    ('Papelería & Arte',     'lapiz',     'Prints, libretas y agendas', 0),
    ('Café & Delicias',      'cafe',      'Granos, salsas y café de especialidad', 0);

-- División territorial: 7 provincias, 84 cantones y 491 distritos
-- Fuente: códigos oficiales de la División Territorial Administrativa de Costa Rica.
INSERT INTO FIDE_PROVINCIA_TB (ID_PROVINCIA, NOMBRE_PROVINCIA) VALUES
    (1, 'San José'),
    (2, 'Alajuela'),
    (3, 'Cartago'),
    (4, 'Heredia'),
    (5, 'Guanacaste'),
    (6, 'Puntarenas'),
    (7, 'Limón');

INSERT INTO FIDE_CANTON_TB (ID_CANTON, NOMBRE_CANTON, ID_PROVINCIA) VALUES
    (101, 'San José', 1),
    (102, 'Escazú', 1),
    (103, 'Desamparados', 1),
    (104, 'Puriscal', 1),
    (105, 'Tarrazú', 1),
    (106, 'Aserrí', 1),
    (107, 'Mora', 1),
    (108, 'Goicoechea', 1),
    (109, 'Santa Ana', 1),
    (110, 'Alajuelita', 1),
    (111, 'Vázquez de Coronado', 1),
    (112, 'Acosta', 1),
    (113, 'Tibás', 1),
    (114, 'Moravia', 1),
    (115, 'Montes de Oca', 1),
    (116, 'Turrubares', 1),
    (117, 'Dota', 1),
    (118, 'Curridabat', 1),
    (119, 'Pérez Zeledón', 1),
    (120, 'León Cortés Castro', 1),
    (201, 'Alajuela', 2),
    (202, 'San Ramón', 2),
    (203, 'Grecia', 2),
    (204, 'San Mateo', 2),
    (205, 'Atenas', 2),
    (206, 'Naranjo', 2),
    (207, 'Palmares', 2),
    (208, 'Poás', 2),
    (209, 'Orotina', 2),
    (210, 'San Carlos', 2),
    (211, 'Zarcero', 2),
    (212, 'Sarchí', 2),
    (213, 'Upala', 2),
    (214, 'Los Chiles', 2),
    (215, 'Guatuso', 2),
    (216, 'Río Cuarto', 2),
    (301, 'Cartago', 3),
    (302, 'Paraíso', 3),
    (303, 'La Unión', 3),
    (304, 'Jiménez', 3),
    (305, 'Turrialba', 3),
    (306, 'Alvarado', 3),
    (307, 'Oreamuno', 3),
    (308, 'El Guarco', 3),
    (401, 'Heredia', 4),
    (402, 'Barva', 4),
    (403, 'Santo Domingo', 4),
    (404, 'Santa Bárbara', 4),
    (405, 'San Rafael', 4),
    (406, 'San Isidro', 4),
    (407, 'Belén', 4),
    (408, 'Flores', 4),
    (409, 'San Pablo', 4),
    (410, 'Sarapiquí', 4),
    (501, 'Liberia', 5),
    (502, 'Nicoya', 5),
    (503, 'Santa Cruz', 5),
    (504, 'Bagaces', 5),
    (505, 'Carrillo', 5),
    (506, 'Cañas', 5),
    (507, 'Abangares', 5),
    (508, 'Tilarán', 5),
    (509, 'Nandayure', 5),
    (510, 'La Cruz', 5),
    (511, 'Hojancha', 5),
    (601, 'Puntarenas', 6),
    (602, 'Esparza', 6),
    (603, 'Buenos Aires', 6),
    (604, 'Montes de Oro', 6),
    (605, 'Osa', 6),
    (606, 'Quepos', 6),
    (607, 'Golfito', 6),
    (608, 'Coto Brus', 6),
    (609, 'Parrita', 6),
    (610, 'Corredores', 6),
    (611, 'Garabito', 6),
    (612, 'Monteverde', 6),
    (613, 'Puerto Jiménez', 6),
    (701, 'Limón', 7),
    (702, 'Pococí', 7),
    (703, 'Siquirres', 7),
    (704, 'Talamanca', 7),
    (705, 'Matina', 7),
    (706, 'Guácimo', 7);

INSERT INTO FIDE_DISTRITO_TB (ID_DISTRITO, NOMBRE_DISTRITO, ID_CANTON) VALUES
    (10101, 'Carmen', 101),
    (10102, 'Merced', 101),
    (10103, 'Hospital', 101),
    (10104, 'Catedral', 101),
    (10105, 'Zapote', 101),
    (10106, 'San Francisco de Dos Ríos', 101),
    (10107, 'Uruca', 101),
    (10108, 'Mata Redonda', 101),
    (10109, 'Pavas', 101),
    (10110, 'Hatillo', 101),
    (10111, 'San Sebastián', 101),
    (10201, 'Escazú', 102),
    (10202, 'San Antonio', 102),
    (10203, 'San Rafael', 102),
    (10301, 'Desamparados', 103),
    (10302, 'San Miguel', 103),
    (10303, 'San Juan de Dios', 103),
    (10304, 'San Rafael Arriba', 103),
    (10305, 'San Antonio', 103),
    (10306, 'Frailes', 103),
    (10307, 'Patarrá', 103),
    (10308, 'San Cristóbal', 103),
    (10309, 'Rosario', 103),
    (10310, 'Damas', 103),
    (10311, 'San Rafael Abajo', 103),
    (10312, 'Gravilias', 103),
    (10313, 'Los Guido', 103),
    (10401, 'Santiago', 104),
    (10402, 'Mercedes Sur', 104),
    (10403, 'Barbacoas', 104),
    (10404, 'Grifo Alto', 104),
    (10405, 'San Rafael', 104),
    (10406, 'Candelarita', 104),
    (10407, 'Desamparaditos', 104),
    (10408, 'San Antonio', 104),
    (10409, 'Chires', 104),
    (10501, 'San Marcos', 105),
    (10502, 'San Lorenzo', 105),
    (10503, 'San Carlos', 105),
    (10601, 'Aserrí', 106),
    (10602, 'Tarbaca', 106),
    (10603, 'Vuelta de Jorco', 106),
    (10604, 'San Gabriel', 106),
    (10605, 'Legua', 106),
    (10606, 'Monterrey', 106),
    (10607, 'Salitrillos', 106),
    (10701, 'Colón', 107),
    (10702, 'Guayabo', 107),
    (10703, 'Tabarcia', 107),
    (10704, 'Piedras Negras', 107),
    (10705, 'Picagres', 107),
    (10706, 'Jaris', 107),
    (10707, 'Quitirrisí', 107),
    (10801, 'Guadalupe', 108),
    (10802, 'San Francisco', 108),
    (10803, 'Calle Blancos', 108),
    (10804, 'Mata de Plátano', 108),
    (10805, 'Ipís', 108),
    (10806, 'Rancho Redondo', 108),
    (10807, 'Purral', 108),
    (10901, 'Santa Ana', 109),
    (10902, 'Salitral', 109),
    (10903, 'Pozos', 109),
    (10904, 'Uruca', 109),
    (10905, 'Piedades', 109),
    (10906, 'Brasil', 109),
    (11001, 'Alajuelita', 110),
    (11002, 'San Josecito', 110),
    (11003, 'San Antonio', 110),
    (11004, 'Concepción', 110),
    (11005, 'San Felipe', 110),
    (11101, 'San Isidro', 111),
    (11102, 'San Rafael', 111),
    (11103, 'Dulce Nombre de Jesús', 111),
    (11104, 'Patalillo', 111),
    (11105, 'Cascajal', 111),
    (11201, 'San Ignacio', 112),
    (11202, 'Guaitil', 112),
    (11203, 'Palmichal', 112),
    (11204, 'Cangrejal', 112),
    (11205, 'Sabanillas', 112),
    (11301, 'San Juan', 113),
    (11302, 'Cinco Esquinas', 113),
    (11303, 'Anselmo Llorente', 113),
    (11304, 'León XIII', 113),
    (11305, 'Colima', 113),
    (11401, 'San Vicente', 114),
    (11402, 'San Jerónimo', 114),
    (11403, 'La Trinidad', 114),
    (11501, 'San Pedro', 115),
    (11502, 'Sabanilla', 115),
    (11503, 'Mercedes', 115),
    (11504, 'San Rafael', 115),
    (11601, 'San Pablo', 116),
    (11602, 'San Pedro', 116),
    (11603, 'San Juan de Mata', 116),
    (11604, 'San Luis', 116),
    (11605, 'Carara', 116),
    (11701, 'Santa María', 117),
    (11702, 'Jardín', 117),
    (11703, 'Copey', 117),
    (11801, 'Curridabat', 118),
    (11802, 'Granadilla', 118),
    (11803, 'Sánchez', 118),
    (11804, 'Tirrases', 118),
    (11901, 'San Isidro de El General', 119),
    (11902, 'El General', 119),
    (11903, 'Daniel Flores', 119),
    (11904, 'Rivas', 119),
    (11905, 'San Pedro', 119),
    (11906, 'Platanares', 119),
    (11907, 'Pejibaye', 119),
    (11908, 'Cajón', 119),
    (11909, 'Barú', 119),
    (11910, 'Río Nuevo', 119),
    (11911, 'Páramo', 119),
    (11912, 'La Amistad', 119),
    (12001, 'San Pablo', 120),
    (12002, 'San Andrés', 120),
    (12003, 'Llano Bonito', 120),
    (12004, 'San Isidro', 120),
    (12005, 'Santa Cruz', 120),
    (12006, 'San Antonio', 120),
    (20101, 'Alajuela', 201),
    (20102, 'San José', 201),
    (20103, 'Carrizal', 201),
    (20104, 'San Antonio', 201),
    (20105, 'Guácima', 201),
    (20106, 'San Isidro', 201),
    (20107, 'Sabanilla', 201),
    (20108, 'San Rafael', 201),
    (20109, 'Río Segundo', 201),
    (20110, 'Desamparados', 201),
    (20111, 'Turrúcares', 201),
    (20112, 'Tambor', 201),
    (20113, 'Garita', 201),
    (20114, 'Sarapiquí', 201),
    (20201, 'San Ramón', 202),
    (20202, 'Santiago', 202),
    (20203, 'San Juan', 202),
    (20204, 'Piedades Norte', 202),
    (20205, 'Piedades Sur', 202),
    (20206, 'San Rafael', 202),
    (20207, 'San Isidro', 202),
    (20208, 'Ángeles', 202),
    (20209, 'Alfaro', 202),
    (20210, 'Volio', 202),
    (20211, 'Concepción', 202),
    (20212, 'Zapotal', 202),
    (20213, 'Peñas Blancas', 202),
    (20214, 'San Lorenzo', 202),
    (20301, 'Grecia', 203),
    (20302, 'San Isidro', 203),
    (20303, 'San José', 203),
    (20304, 'San Roque', 203),
    (20305, 'Tacares', 203),
    (20307, 'Puente de Piedra', 203),
    (20308, 'Bolívar', 203),
    (20401, 'San Mateo', 204),
    (20402, 'Desmonte', 204),
    (20403, 'Jesús María', 204),
    (20404, 'Labrador', 204),
    (20501, 'Atenas', 205),
    (20502, 'Jesús', 205),
    (20503, 'Mercedes', 205),
    (20504, 'San Isidro', 205),
    (20505, 'Concepción', 205),
    (20506, 'San José', 205),
    (20507, 'Santa Eulalia', 205),
    (20508, 'Escobal', 205),
    (20601, 'Naranjo', 206),
    (20602, 'San Miguel', 206),
    (20603, 'San José', 206),
    (20604, 'Cirrí Sur', 206),
    (20605, 'San Jerónimo', 206),
    (20606, 'San Juan', 206),
    (20607, 'El Rosario', 206),
    (20608, 'Palmitos', 206),
    (20701, 'Palmares', 207),
    (20702, 'Zaragoza', 207),
    (20703, 'Buenos Aires', 207),
    (20704, 'Santiago', 207),
    (20705, 'Candelaria', 207),
    (20706, 'Esquipulas', 207),
    (20707, 'La Granja', 207),
    (20801, 'San Pedro', 208),
    (20802, 'San Juan', 208),
    (20803, 'San Rafael', 208),
    (20804, 'Carrillos', 208),
    (20805, 'Sabana Redonda', 208),
    (20901, 'Orotina', 209),
    (20902, 'El Mastate', 209),
    (20903, 'Hacienda Vieja', 209),
    (20904, 'Coyolar', 209),
    (20905, 'La Ceiba', 209),
    (21001, 'Quesada', 210),
    (21002, 'Florencia', 210),
    (21003, 'Buenavista', 210),
    (21004, 'Aguas Zarcas', 210),
    (21005, 'Venecia', 210),
    (21006, 'Pital', 210),
    (21007, 'La Fortuna', 210),
    (21008, 'La Tigra', 210),
    (21009, 'La Palmera', 210),
    (21010, 'Venado', 210),
    (21011, 'Cutris', 210),
    (21012, 'Monterrey', 210),
    (21013, 'Pocosol', 210),
    (21101, 'Zarcero', 211),
    (21102, 'Laguna', 211),
    (21103, 'Tapesco', 211),
    (21104, 'Guadalupe', 211),
    (21105, 'Palmira', 211),
    (21106, 'Zapote', 211),
    (21107, 'Brisas', 211),
    (21201, 'Sarchí Norte', 212),
    (21202, 'Sarchí Sur', 212),
    (21203, 'Toro Amarillo', 212),
    (21204, 'San Pedro', 212),
    (21205, 'Rodríguez', 212),
    (21301, 'Upala', 213),
    (21302, 'Aguas Claras', 213),
    (21303, 'San José', 213),
    (21304, 'Bijagua', 213),
    (21305, 'Delicias', 213),
    (21306, 'Dos Ríos', 213),
    (21307, 'Yolillal', 213),
    (21308, 'Canalete', 213),
    (21401, 'Los Chiles', 214),
    (21402, 'Caño Negro', 214),
    (21403, 'El Amparo', 214),
    (21404, 'San Jorge', 214),
    (21501, 'San Rafael', 215),
    (21502, 'Buenavista', 215),
    (21503, 'Cote', 215),
    (21504, 'Katira', 215),
    (21601, 'Río Cuarto', 216),
    (21602, 'Santa Rita', 216),
    (21603, 'Santa Isabel', 216),
    (30101, 'Oriental', 301),
    (30102, 'Occidental', 301),
    (30103, 'Carmen', 301),
    (30104, 'San Nicolás', 301),
    (30105, 'Aguacaliente', 301),
    (30106, 'Guadalupe', 301),
    (30107, 'Corralillo', 301),
    (30108, 'Tierra Blanca', 301),
    (30109, 'Dulce Nombre', 301),
    (30110, 'Llano Grande', 301),
    (30111, 'Quebradilla', 301),
    (30201, 'Paraíso', 302),
    (30202, 'Santiago', 302),
    (30203, 'Orosi', 302),
    (30204, 'Cachí', 302),
    (30205, 'Llanos de Santa Lucía', 302),
    (30206, 'Birrisito', 302),
    (30301, 'Tres Ríos', 303),
    (30302, 'San Diego', 303),
    (30303, 'San Juan', 303),
    (30304, 'San Rafael', 303),
    (30305, 'Concepción', 303),
    (30306, 'Dulce Nombre', 303),
    (30307, 'San Ramón', 303),
    (30308, 'Río Azul', 303),
    (30401, 'Juan Viñas', 304),
    (30402, 'Tucurrique', 304),
    (30403, 'Pejibaye', 304),
    (30404, 'La Victoria', 304),
    (30501, 'Turrialba', 305),
    (30502, 'La Suiza', 305),
    (30503, 'Peralta', 305),
    (30504, 'Santa Cruz', 305),
    (30505, 'Santa Teresita', 305),
    (30506, 'Pavones', 305),
    (30507, 'Tuis', 305),
    (30508, 'Tayutic', 305),
    (30509, 'Santa Rosa', 305),
    (30510, 'Tres Equis', 305),
    (30511, 'La Isabel', 305),
    (30512, 'Chirripó', 305),
    (30601, 'Pacayas', 306),
    (30602, 'Cervantes', 306),
    (30603, 'Capellades', 306),
    (30701, 'San Rafael', 307),
    (30702, 'Cot', 307),
    (30703, 'Potrero Cerrado', 307),
    (30704, 'Cipreses', 307),
    (30705, 'Santa Rosa', 307),
    (30801, 'El Tejar', 308),
    (30802, 'San Isidro', 308),
    (30803, 'Tobosi', 308),
    (30804, 'Patio de Agua', 308),
    (40101, 'Heredia', 401),
    (40102, 'Mercedes', 401),
    (40103, 'San Francisco', 401),
    (40104, 'Ulloa', 401),
    (40105, 'Varablanca', 401),
    (40201, 'Barva', 402),
    (40202, 'San Pedro', 402),
    (40203, 'San Pablo', 402),
    (40204, 'San Roque', 402),
    (40205, 'Santa Lucía', 402),
    (40206, 'San José de la Montaña', 402),
    (40207, 'Puente Salas', 402),
    (40301, 'Santo Domingo', 403),
    (40302, 'San Vicente', 403),
    (40303, 'San Miguel', 403),
    (40304, 'Paracito', 403),
    (40305, 'Santo Tomás', 403),
    (40306, 'Santa Rosa', 403),
    (40307, 'Tures', 403),
    (40308, 'Pará', 403),
    (40401, 'Santa Bárbara', 404),
    (40402, 'San Pedro', 404),
    (40403, 'San Juan', 404),
    (40404, 'Jesús', 404),
    (40405, 'Santo Domingo', 404),
    (40406, 'Purabá', 404),
    (40501, 'San Rafael', 405),
    (40502, 'San Josecito', 405),
    (40503, 'Santiago', 405),
    (40504, 'Ángeles', 405),
    (40505, 'Concepción', 405),
    (40601, 'San Isidro', 406),
    (40602, 'San José', 406),
    (40603, 'Concepción', 406),
    (40604, 'San Francisco', 406),
    (40701, 'San Antonio', 407),
    (40702, 'La Ribera', 407),
    (40703, 'La Asunción', 407),
    (40801, 'San Joaquín', 408),
    (40802, 'Barrantes', 408),
    (40803, 'Llorente', 408),
    (40901, 'San Pablo', 409),
    (40902, 'Rincón de Sabanilla', 409),
    (41001, 'Puerto Viejo', 410),
    (41002, 'La Virgen', 410),
    (41003, 'Las Horquetas', 410),
    (41004, 'Llanuras del Gaspar', 410),
    (41005, 'Cureña', 410),
    (50101, 'Liberia', 501),
    (50102, 'Cañas Dulces', 501),
    (50103, 'Mayorga', 501),
    (50104, 'Nacascolo', 501),
    (50105, 'Curubandé', 501),
    (50201, 'Nicoya', 502),
    (50202, 'Mansión', 502),
    (50203, 'San Antonio', 502),
    (50204, 'Quebrada Honda', 502),
    (50205, 'Sámara', 502),
    (50206, 'Nosara', 502),
    (50207, 'Belén de Nosarita', 502),
    (50301, 'Santa Cruz', 503),
    (50302, 'Bolsón', 503),
    (50303, 'Veintisiete de Abril', 503),
    (50304, 'Tempate', 503),
    (50305, 'Cartagena', 503),
    (50306, 'Cuajiniquil', 503),
    (50307, 'Diriá', 503),
    (50308, 'Cabo Velas', 503),
    (50309, 'Tamarindo', 503),
    (50401, 'Bagaces', 504),
    (50402, 'La Fortuna', 504),
    (50403, 'Mogote', 504),
    (50404, 'Río Naranjo', 504),
    (50501, 'Filadelfia', 505),
    (50502, 'Palmira', 505),
    (50503, 'Sardinal', 505),
    (50504, 'Belén', 505),
    (50601, 'Cañas', 506),
    (50602, 'Palmira', 506),
    (50603, 'San Miguel', 506),
    (50604, 'Bebedero', 506),
    (50605, 'Porozal', 506),
    (50701, 'Las Juntas', 507),
    (50702, 'Sierra', 507),
    (50703, 'San Juan', 507),
    (50704, 'Colorado', 507),
    (50801, 'Tilarán', 508),
    (50802, 'Quebrada Grande', 508),
    (50803, 'Tronadora', 508),
    (50804, 'Santa Rosa', 508),
    (50805, 'Líbano', 508),
    (50806, 'Tierras Morenas', 508),
    (50807, 'Arenal', 508),
    (50808, 'Cabeceras', 508),
    (50901, 'Carmona', 509),
    (50902, 'Santa Rita', 509),
    (50903, 'Zapotal', 509),
    (50904, 'San Pablo', 509),
    (50905, 'Porvenir', 509),
    (50906, 'Bejuco', 509),
    (51001, 'La Cruz', 510),
    (51002, 'Santa Cecilia', 510),
    (51003, 'La Garita', 510),
    (51004, 'Santa Elena', 510),
    (51101, 'Hojancha', 511),
    (51102, 'Monte Romo', 511),
    (51103, 'Puerto Carrillo', 511),
    (51104, 'Huacas', 511),
    (51105, 'Matambú', 511),
    (60101, 'Puntarenas', 601),
    (60102, 'Pitahaya', 601),
    (60103, 'Chomes', 601),
    (60104, 'Lepanto', 601),
    (60105, 'Paquera', 601),
    (60106, 'Manzanillo', 601),
    (60107, 'Guacimal', 601),
    (60108, 'Barranca', 601),
    (60110, 'Isla del Coco', 601),
    (60111, 'Cóbano', 601),
    (60112, 'Chacarita', 601),
    (60113, 'Chira', 601),
    (60114, 'Acapulco', 601),
    (60115, 'El Roble', 601),
    (60116, 'Arancibia', 601),
    (60201, 'Espíritu Santo', 602),
    (60202, 'San Juan Grande', 602),
    (60203, 'Macacona', 602),
    (60204, 'San Rafael', 602),
    (60205, 'San Jerónimo', 602),
    (60206, 'Caldera', 602),
    (60301, 'Buenos Aires', 603),
    (60302, 'Volcán', 603),
    (60303, 'Potrero Grande', 603),
    (60304, 'Boruca', 603),
    (60305, 'Pilas', 603),
    (60306, 'Colinas', 603),
    (60307, 'Chánguena', 603),
    (60308, 'Biolley', 603),
    (60309, 'Brunka', 603),
    (60401, 'Miramar', 604),
    (60402, 'La Unión', 604),
    (60403, 'San Isidro', 604),
    (60501, 'Puerto Cortés', 605),
    (60502, 'Palmar', 605),
    (60503, 'Sierpe', 605),
    (60504, 'Bahía Ballena', 605),
    (60505, 'Piedras Blancas', 605),
    (60506, 'Bahía Drake', 605),
    (60601, 'Quepos', 606),
    (60602, 'Savegre', 606),
    (60603, 'Naranjito', 606),
    (60701, 'Golfito', 607),
    (60703, 'Guaycará', 607),
    (60704, 'Pavón', 607),
    (60801, 'San Vito', 608),
    (60802, 'Sabalito', 608),
    (60803, 'Aguabuena', 608),
    (60804, 'Limoncito', 608),
    (60805, 'Pittier', 608),
    (60806, 'Gutiérrez Braun', 608),
    (60901, 'Parrita', 609),
    (61001, 'Corredor', 610),
    (61002, 'La Cuesta', 610),
    (61003, 'Canoas', 610),
    (61004, 'Laurel', 610),
    (61101, 'Jacó', 611),
    (61102, 'Tárcoles', 611),
    (61201, 'Monteverde', 612),
    (61301, 'Puerto Jiménez', 613),
    (70101, 'Limón', 701),
    (70102, 'Valle La Estrella', 701),
    (70103, 'Río Blanco', 701),
    (70104, 'Matama', 701),
    (70201, 'Guápiles', 702),
    (70202, 'Jiménez', 702),
    (70203, 'Rita', 702),
    (70204, 'Roxana', 702),
    (70205, 'Cariari', 702),
    (70206, 'Colorado', 702),
    (70207, 'La Colonia', 702),
    (70301, 'Siquirres', 703),
    (70302, 'Pacuarito', 703),
    (70303, 'Florida', 703),
    (70304, 'Germania', 703),
    (70305, 'El Cairo', 703),
    (70306, 'Alegría', 703),
    (70307, 'Reventazón', 703),
    (70401, 'Bratsi', 704),
    (70402, 'Sixaola', 704),
    (70403, 'Cahuita', 704),
    (70404, 'Telire', 704),
    (70501, 'Matina', 705),
    (70502, 'Batán', 705),
    (70503, 'Carrandi', 705),
    (70601, 'Guácimo', 706),
    (70602, 'Mercedes', 706),
    (70603, 'Pocora', 706),
    (70604, 'Río Jiménez', 706),
    (70605, 'Duacarí', 706);
