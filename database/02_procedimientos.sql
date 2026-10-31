-- ============================================================
-- David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
-- Ambiente Web Cliente/Servidor · SC-502
-- Universidad Fidélitas · Costa Rica
-- ============================================================
--  VENTICA · 02_procedimientos.sql
--  Procedimientos almacenados (prefijo sp_<tabla>_<accion>).
--  Los repositorios de PHP SOLO ejecutan estos procedimientos
--  con CALL (nunca SQL suelto en el código).
--
--  Trabajan sobre las tablas FIDE_..._TB y las vistas FIDE_..._V de
--  01_tablas.sql. Los estados se reciben y se devuelven por nombre
--  ('activo', 'pendiente'...) y aquí se traducen a su ID_ESTADO.
--
--  Se corre después de 01_tablas.sql:
--    php database/instalar.php   (corre los 3 scripts en orden)
--
--  Secciones:
--    1. Ubicación y direcciones
--    2. Rol y usuario
--    3. Emprendimiento
--    4. Categoría
--    5. Producto e imágenes
--    6. Reseñas y favoritos
--    7. Carrito
--    8. Pedidos
--    9. Reportes
-- ============================================================

DELIMITER $$

USE ventica$$

-- Procedimientos del modelo anterior que ya no existen
DROP PROCEDURE IF EXISTS sp_direccion_entrega_insertar$$

-- ============================================================
-- 1. UBICACIÓN Y DIRECCIONES
-- ============================================================

-- Todos los distritos con su cantón y provincia (para los selects en cascada)
DROP PROCEDURE IF EXISTS sp_ubicacion_listar$$
CREATE PROCEDURE sp_ubicacion_listar()
BEGIN
    SELECT id_provincia, provincia, id_canton, canton, id_distrito, distrito
    FROM FIDE_UBICACION_V
    ORDER BY id_provincia, canton, distrito;
END$$

-- Un distrito con su cantón y provincia (para validar lo que llega del formulario)
DROP PROCEDURE IF EXISTS sp_ubicacion_obtener$$
CREATE PROCEDURE sp_ubicacion_obtener(IN p_id_distrito INT)
BEGIN
    SELECT id_provincia, provincia, id_canton, canton, id_distrito, distrito
    FROM FIDE_UBICACION_V
    WHERE id_distrito = p_id_distrito;
END$$

-- Crea una dirección (distrito + otras señas). El código postal es el código del distrito.
DROP PROCEDURE IF EXISTS sp_direccion_insertar$$
CREATE PROCEDURE sp_direccion_insertar(IN p_id_distrito INT, IN p_otras_senas VARCHAR(255))
BEGIN
    INSERT INTO FIDE_DIRECCION_TB (ID_DISTRITO, OTRAS_SENAS, CODIGO_POSTAL)
    VALUES (p_id_distrito, NULLIF(p_otras_senas, ''), p_id_distrito);

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Guarda la dirección principal de un usuario. Si es igual a la que ya tiene no hace nada;
-- si cambió crea una nueva (los pedidos viejos conservan la suya).
-- p_id_distrito = 0 quita la dirección principal.
DROP PROCEDURE IF EXISTS sp_usuario_guardar_direccion$$
CREATE PROCEDURE sp_usuario_guardar_direccion(
    IN p_id_usuario INT,
    IN p_id_distrito INT,
    IN p_otras_senas VARCHAR(255)
)
BEGIN
    DECLARE v_iguales INT DEFAULT 0;
    DECLARE v_id_direccion INT;

    SELECT COUNT(*) INTO v_iguales
    FROM FIDE_USUARIO_DIRECCION_TB ud
    INNER JOIN FIDE_DIRECCION_TB d ON d.ID_DIRECCION = ud.ID_DIRECCION
    WHERE ud.ID_USUARIO = p_id_usuario AND ud.ES_PRINCIPAL = 1
      AND d.ID_DISTRITO = p_id_distrito
      AND COALESCE(d.OTRAS_SENAS, '') = p_otras_senas;

    IF v_iguales = 0 THEN
        UPDATE FIDE_USUARIO_DIRECCION_TB SET ES_PRINCIPAL = 0 WHERE ID_USUARIO = p_id_usuario;

        IF p_id_distrito > 0 THEN
            INSERT INTO FIDE_DIRECCION_TB (ID_DISTRITO, OTRAS_SENAS, CODIGO_POSTAL)
            VALUES (p_id_distrito, NULLIF(p_otras_senas, ''), p_id_distrito);
            SET v_id_direccion = LAST_INSERT_ID();

            INSERT INTO FIDE_USUARIO_DIRECCION_TB (ID_USUARIO, ID_DIRECCION, ALIAS, ES_PRINCIPAL)
            VALUES (p_id_usuario, v_id_direccion, 'Principal', 1);
        END IF;
    END IF;
END$$

-- ============================================================
-- 2. ROL Y USUARIO
-- ============================================================

-- Busca un rol por su nombre (Cliente, Emprendedor, Administrador)
DROP PROCEDURE IF EXISTS sp_rol_obtener_por_nombre$$
CREATE PROCEDURE sp_rol_obtener_por_nombre(IN p_nombre VARCHAR(30))
BEGIN
    SELECT ID_ROL, NOMBRE_ROL, DESCRIPCION
    FROM FIDE_ROL_TB
    WHERE NOMBRE_ROL = p_nombre;
END$$

-- Usuario por correo (para el login). Incluye su rol y su emprendimiento si tiene.
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_correo$$
CREATE PROCEDURE sp_usuario_obtener_por_correo(IN p_correo VARCHAR(150))
BEGIN
    SELECT u.*, e.id_emprendimiento, e.nombre_negocio, e.estado_verificacion
    FROM FIDE_CORREO_TB c
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = c.ID_USUARIO
    LEFT JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_usuario = u.id_usuario
    WHERE c.CORREO = p_correo;
END$$

-- Usuario por cédula (para no repetirla al registrarse)
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_cedula$$
CREATE PROCEDURE sp_usuario_obtener_por_cedula(IN p_cedula VARCHAR(12))
BEGIN
    SELECT ID_USUARIO, CEDULA
    FROM FIDE_USUARIO_TB
    WHERE CEDULA = p_cedula;
END$$

-- Usuario por id
DROP PROCEDURE IF EXISTS sp_usuario_obtener$$
CREATE PROCEDURE sp_usuario_obtener(IN p_id_usuario INT)
BEGIN
    SELECT u.*, e.id_emprendimiento, e.nombre_negocio, e.estado_verificacion
    FROM FIDE_USUARIO_V u
    LEFT JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_usuario = u.id_usuario
    WHERE u.id_usuario = p_id_usuario;
END$$

-- Crea un usuario (activo) con su correo y su celular. Devuelve el id.
DROP PROCEDURE IF EXISTS sp_usuario_insertar$$
CREATE PROCEDURE sp_usuario_insertar(
    IN p_id_rol INT,
    IN p_cedula VARCHAR(12),
    IN p_nombre VARCHAR(60),
    IN p_primer_apellido VARCHAR(40),
    IN p_segundo_apellido VARCHAR(40),
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255),
    IN p_telefono VARCHAR(15)
)
BEGIN
    DECLARE v_id INT;

    INSERT INTO FIDE_USUARIO_TB (CEDULA, NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO, CONTRASENA_HASH, ID_ROL, ID_ESTADO)
    VALUES (p_cedula, p_nombre, p_primer_apellido, NULLIF(p_segundo_apellido, ''), p_contrasena, p_id_rol,
            (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'activo'));
    SET v_id = LAST_INSERT_ID();

    INSERT INTO FIDE_CORREO_TB (CORREO, ID_USUARIO) VALUES (p_correo, v_id);

    IF p_telefono <> '' THEN
        INSERT INTO FIDE_TELEFONO_TB (NUMERO, ID_TIPO_TELEFONO, ID_USUARIO) VALUES (p_telefono, 1, v_id);
    END IF;

    SELECT v_id AS id;
END$$

-- Cambia el correo principal de un usuario
DROP PROCEDURE IF EXISTS sp_correo_actualizar_usuario$$
CREATE PROCEDURE sp_correo_actualizar_usuario(IN p_id_usuario INT, IN p_correo VARCHAR(150))
BEGIN
    DECLARE v_id_correo INT DEFAULT NULL;

    SELECT ID_CORREO INTO v_id_correo
    FROM FIDE_CORREO_TB
    WHERE ID_USUARIO = p_id_usuario
    ORDER BY ID_CORREO LIMIT 1;

    IF v_id_correo IS NULL THEN
        INSERT INTO FIDE_CORREO_TB (CORREO, ID_USUARIO) VALUES (p_correo, p_id_usuario);
    ELSE
        UPDATE FIDE_CORREO_TB SET CORREO = p_correo WHERE ID_CORREO = v_id_correo;
    END IF;
END$$

-- Cambia el celular de un usuario ('' lo borra)
DROP PROCEDURE IF EXISTS sp_telefono_actualizar_usuario$$
CREATE PROCEDURE sp_telefono_actualizar_usuario(IN p_id_usuario INT, IN p_telefono VARCHAR(15))
BEGIN
    DECLARE v_id_telefono INT DEFAULT NULL;

    SELECT ID_TELEFONO INTO v_id_telefono
    FROM FIDE_TELEFONO_TB
    WHERE ID_USUARIO = p_id_usuario
    ORDER BY ID_TELEFONO LIMIT 1;

    IF p_telefono = '' THEN
        DELETE FROM FIDE_TELEFONO_TB WHERE ID_TELEFONO = v_id_telefono;
    ELSEIF v_id_telefono IS NULL THEN
        INSERT INTO FIDE_TELEFONO_TB (NUMERO, ID_TIPO_TELEFONO, ID_USUARIO) VALUES (p_telefono, 1, p_id_usuario);
    ELSE
        UPDATE FIDE_TELEFONO_TB SET NUMERO = p_telefono WHERE ID_TELEFONO = v_id_telefono;
    END IF;
END$$

-- Actualiza los datos personales y la dirección principal (Mi perfil)
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_perfil$$
CREATE PROCEDURE sp_usuario_actualizar_perfil(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(60),
    IN p_primer_apellido VARCHAR(40),
    IN p_segundo_apellido VARCHAR(40),
    IN p_correo VARCHAR(150),
    IN p_telefono VARCHAR(15),
    IN p_id_distrito INT,
    IN p_otras_senas VARCHAR(255)
)
BEGIN
    UPDATE FIDE_USUARIO_TB
    SET NOMBRE           = p_nombre,
        PRIMER_APELLIDO  = p_primer_apellido,
        SEGUNDO_APELLIDO = NULLIF(p_segundo_apellido, '')
    WHERE ID_USUARIO = p_id_usuario;

    CALL sp_correo_actualizar_usuario(p_id_usuario, p_correo);
    CALL sp_telefono_actualizar_usuario(p_id_usuario, p_telefono);
    CALL sp_usuario_guardar_direccion(p_id_usuario, p_id_distrito, p_otras_senas);
END$$

-- Cambia la contraseña (ya viene cifrada desde PHP)
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_contrasena$$
CREATE PROCEDURE sp_usuario_actualizar_contrasena(IN p_id_usuario INT, IN p_contrasena VARCHAR(255))
BEGIN
    UPDATE FIDE_USUARIO_TB SET CONTRASENA_HASH = p_contrasena WHERE ID_USUARIO = p_id_usuario;
END$$

-- Activa o bloquea (suspende) una cuenta. No se borra nada.
DROP PROCEDURE IF EXISTS sp_usuario_cambiar_estado$$
CREATE PROCEDURE sp_usuario_cambiar_estado(IN p_id_usuario INT, IN p_estado VARCHAR(20))
BEGIN
    UPDATE FIDE_USUARIO_TB
    SET ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = p_estado)
    WHERE ID_USUARIO = p_id_usuario;
END$$

-- Datos que el administrador puede editar de una cuenta
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_admin$$
CREATE PROCEDURE sp_usuario_actualizar_admin(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(60),
    IN p_primer_apellido VARCHAR(40),
    IN p_segundo_apellido VARCHAR(40),
    IN p_correo VARCHAR(150)
)
BEGIN
    UPDATE FIDE_USUARIO_TB
    SET NOMBRE           = p_nombre,
        PRIMER_APELLIDO  = p_primer_apellido,
        SEGUNDO_APELLIDO = NULLIF(p_segundo_apellido, '')
    WHERE ID_USUARIO = p_id_usuario;

    CALL sp_correo_actualizar_usuario(p_id_usuario, p_correo);
END$$

-- Clientes y emprendedores para Gestión de Usuarios (el admin no se lista)
DROP PROCEDURE IF EXISTS sp_usuario_listar_admin$$
CREATE PROCEDURE sp_usuario_listar_admin()
BEGIN
    SELECT u.id_usuario, u.cedula, u.nombre, u.nombre_pila, u.primer_apellido, u.segundo_apellido,
           u.correo, u.fecha_registro, u.estado, u.nombre_rol, e.nombre_negocio
    FROM FIDE_USUARIO_V u
    LEFT JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_usuario = u.id_usuario
    WHERE u.nombre_rol <> 'Administrador'
    ORDER BY u.fecha_registro DESC;
END$$

-- Totales de cuentas para las pestañas de Gestión de Usuarios
DROP PROCEDURE IF EXISTS sp_usuario_contar$$
CREATE PROCEDURE sp_usuario_contar()
BEGIN
    SELECT
        SUM(CASE WHEN nombre_rol <> 'Administrador' THEN 1 ELSE 0 END) AS todos,
        SUM(CASE WHEN nombre_rol = 'Cliente' THEN 1 ELSE 0 END) AS clientes,
        SUM(CASE WHEN nombre_rol = 'Emprendedor' THEN 1 ELSE 0 END) AS emprendedores,
        SUM(CASE WHEN nombre_rol <> 'Administrador' AND estado <> 'activo' THEN 1 ELSE 0 END) AS suspendidos
    FROM FIDE_USUARIO_V;
END$$

-- ============================================================
-- 3. EMPRENDIMIENTO
-- ============================================================

-- Cambia las formas de pago de un emprendimiento. p_metodos: 'sinpe,tarjeta'
DROP PROCEDURE IF EXISTS sp_emprendimiento_guardar_metodos$$
CREATE PROCEDURE sp_emprendimiento_guardar_metodos(IN p_id_emprendimiento INT, IN p_metodos VARCHAR(60))
BEGIN
    DELETE FROM FIDE_EMPRENDIMIENTO_METODO_PAGO_TB WHERE ID_EMPRENDIMIENTO = p_id_emprendimiento;

    INSERT INTO FIDE_EMPRENDIMIENTO_METODO_PAGO_TB (ID_EMPRENDIMIENTO, ID_METODO_PAGO)
    SELECT p_id_emprendimiento, ID_METODO_PAGO
    FROM FIDE_METODO_PAGO_TB
    WHERE FIND_IN_SET(NOMBRE_METODO, p_metodos) > 0;
END$$

-- Registra la solicitud de un emprendimiento (queda pendiente) con su dirección.
-- p_id_distrito = 0: sin dirección todavía (cuando lo invita el admin)
DROP PROCEDURE IF EXISTS sp_emprendimiento_insertar$$
CREATE PROCEDURE sp_emprendimiento_insertar(
    IN p_id_usuario INT,
    IN p_nombre_negocio VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_id_distrito INT,
    IN p_otras_senas VARCHAR(255),
    IN p_logo VARCHAR(255),
    IN p_metodos_pago VARCHAR(60)
)
BEGIN
    DECLARE v_id_direccion INT DEFAULT NULL;
    DECLARE v_id INT;

    IF p_id_distrito > 0 THEN
        INSERT INTO FIDE_DIRECCION_TB (ID_DISTRITO, OTRAS_SENAS, CODIGO_POSTAL)
        VALUES (p_id_distrito, NULLIF(p_otras_senas, ''), p_id_distrito);
        SET v_id_direccion = LAST_INSERT_ID();
    END IF;

    INSERT INTO FIDE_EMPRENDIMIENTO_TB (ID_USUARIO, NOMBRE_NEGOCIO, DESCRIPCION, LOGO_URL, ID_DIRECCION, ID_ESTADO)
    VALUES (p_id_usuario, p_nombre_negocio, p_descripcion, NULLIF(p_logo, ''), v_id_direccion,
            (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'pendiente'));
    SET v_id = LAST_INSERT_ID();

    CALL sp_emprendimiento_guardar_metodos(v_id, p_metodos_pago);

    SELECT v_id AS id;
END$$

-- Emprendimiento completo con los datos del dueño, calificación y totales
DROP PROCEDURE IF EXISTS sp_emprendimiento_obtener$$
CREATE PROCEDURE sp_emprendimiento_obtener(IN p_id_emprendimiento INT)
BEGIN
    SELECT e.*,
           u.nombre AS dueno, u.correo, u.telefono, u.estado AS estado_usuario,
           (SELECT COUNT(*) FROM FIDE_PRODUCTO_V p
             WHERE p.id_emprendimiento = e.id_emprendimiento AND p.estado <> 'inactivo') AS total_productos,
           (SELECT ROUND(AVG(r.CALIFICACION), 1) FROM FIDE_RESENA_TB r
             INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = r.ID_PRODUCTO
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento) AS calificacion,
           (SELECT COUNT(*) FROM FIDE_RESENA_TB r
             INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = r.ID_PRODUCTO
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento) AS total_resenas,
           (SELECT c.NOMBRE_CATEGORIA FROM FIDE_PRODUCTO_TB p
             INNER JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento
             GROUP BY c.ID_CATEGORIA, c.NOMBRE_CATEGORIA
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal
    FROM FIDE_EMPRENDIMIENTO_V e
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE e.id_emprendimiento = p_id_emprendimiento;
END$$

-- Emprendimiento de un usuario (portal del vendedor)
DROP PROCEDURE IF EXISTS sp_emprendimiento_obtener_por_usuario$$
CREATE PROCEDURE sp_emprendimiento_obtener_por_usuario(IN p_id_usuario INT)
BEGIN
    DECLARE v_id INT DEFAULT NULL;

    SELECT ID_EMPRENDIMIENTO INTO v_id
    FROM FIDE_EMPRENDIMIENTO_TB
    WHERE ID_USUARIO = p_id_usuario;

    CALL sp_emprendimiento_obtener(v_id);
END$$

-- Actualiza la vitrina (Mi perfil del vendedor). Si p_logo viene vacío, se deja el actual.
-- La dirección se actualiza en su propia fila de FIDE_DIRECCION_TB (p_id_distrito = 0 la deja igual).
DROP PROCEDURE IF EXISTS sp_emprendimiento_actualizar$$
CREATE PROCEDURE sp_emprendimiento_actualizar(
    IN p_id_emprendimiento INT,
    IN p_nombre_negocio VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_id_distrito INT,
    IN p_otras_senas VARCHAR(255),
    IN p_logo VARCHAR(255),
    IN p_metodos_pago VARCHAR(60)
)
BEGIN
    DECLARE v_id_direccion INT DEFAULT NULL;

    SELECT ID_DIRECCION INTO v_id_direccion
    FROM FIDE_EMPRENDIMIENTO_TB
    WHERE ID_EMPRENDIMIENTO = p_id_emprendimiento;

    IF p_id_distrito > 0 AND v_id_direccion IS NULL THEN
        INSERT INTO FIDE_DIRECCION_TB (ID_DISTRITO, OTRAS_SENAS, CODIGO_POSTAL)
        VALUES (p_id_distrito, NULLIF(p_otras_senas, ''), p_id_distrito);
        SET v_id_direccion = LAST_INSERT_ID();
    ELSEIF p_id_distrito > 0 THEN
        UPDATE FIDE_DIRECCION_TB
        SET ID_DISTRITO = p_id_distrito, OTRAS_SENAS = NULLIF(p_otras_senas, ''), CODIGO_POSTAL = p_id_distrito
        WHERE ID_DIRECCION = v_id_direccion;
    END IF;

    UPDATE FIDE_EMPRENDIMIENTO_TB
    SET NOMBRE_NEGOCIO = p_nombre_negocio,
        DESCRIPCION    = p_descripcion,
        ID_DIRECCION   = v_id_direccion,
        LOGO_URL       = IF(p_logo = '', LOGO_URL, p_logo)
    WHERE ID_EMPRENDIMIENTO = p_id_emprendimiento;

    CALL sp_emprendimiento_guardar_metodos(p_id_emprendimiento, p_metodos_pago);
END$$

-- Lista emprendimientos por estado (pendiente, aprobado, rechazado) para el admin
DROP PROCEDURE IF EXISTS sp_emprendimiento_listar_por_estado$$
CREATE PROCEDURE sp_emprendimiento_listar_por_estado(IN p_estado VARCHAR(20))
BEGIN
    SELECT e.*,
           u.nombre AS dueno, u.cedula, u.correo, u.telefono, u.estado AS estado_usuario,
           (SELECT COUNT(*) FROM FIDE_PRODUCTO_TB p WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento) AS total_productos,
           (SELECT c.NOMBRE_CATEGORIA FROM FIDE_PRODUCTO_TB p
             INNER JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento
             GROUP BY c.ID_CATEGORIA, c.NOMBRE_CATEGORIA
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal,
           (SELECT ROUND(AVG(r.CALIFICACION), 1) FROM FIDE_RESENA_TB r
             INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = r.ID_PRODUCTO
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento) AS calificacion
    FROM FIDE_EMPRENDIMIENTO_V e
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE e.estado_verificacion = p_estado
    ORDER BY e.fecha_solicitud DESC;
END$$

-- Aprueba o rechaza una solicitud, guarda la observación y la fecha de verificación
DROP PROCEDURE IF EXISTS sp_emprendimiento_resolver$$
CREATE PROCEDURE sp_emprendimiento_resolver(
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(20),
    IN p_observacion VARCHAR(500)
)
BEGIN
    UPDATE FIDE_EMPRENDIMIENTO_TB
    SET ID_ESTADO          = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = p_estado),
        OBSERVACION        = NULLIF(p_observacion, ''),
        FECHA_VERIFICACION = NOW()
    WHERE ID_EMPRENDIMIENTO = p_id_emprendimiento;
END$$

-- Emprendimientos aprobados y activos para "Historias de Artesanos"
DROP PROCEDURE IF EXISTS sp_emprendimiento_listar_historias$$
CREATE PROCEDURE sp_emprendimiento_listar_historias()
BEGIN
    SELECT e.id_emprendimiento, e.nombre_negocio, e.descripcion, e.ubicacion, e.logo,
           e.fecha_solicitud, u.nombre AS dueno,
           (SELECT COUNT(*) FROM FIDE_PRODUCTO_V p
             WHERE p.id_emprendimiento = e.id_emprendimiento AND p.estado = 'activo') AS total_productos,
           (SELECT ROUND(AVG(r.CALIFICACION), 1) FROM FIDE_RESENA_TB r
             INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = r.ID_PRODUCTO
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento) AS calificacion,
           (SELECT i.URL_IMAGEN FROM FIDE_PRODUCTO_TB p
             INNER JOIN FIDE_IMAGEN_PRODUCTO_TB i ON i.ID_PRODUCTO = p.ID_PRODUCTO
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento
             ORDER BY p.ID_PRODUCTO, i.ORDEN LIMIT 1) AS imagen,
           (SELECT c.NOMBRE_CATEGORIA FROM FIDE_PRODUCTO_TB p
             INNER JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA
             WHERE p.ID_EMPRENDIMIENTO = e.id_emprendimiento
             GROUP BY c.ID_CATEGORIA, c.NOMBRE_CATEGORIA
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal
    FROM FIDE_EMPRENDIMIENTO_V e
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE e.estado_verificacion = 'aprobado' AND u.estado = 'activo'
    ORDER BY e.fecha_solicitud;
END$$

-- Redes sociales de un emprendimiento (vitrina)
DROP PROCEDURE IF EXISTS sp_red_social_listar$$
CREATE PROCEDURE sp_red_social_listar(IN p_id_emprendimiento INT)
BEGIN
    SELECT ID_RED_SOCIAL, PLATAFORMA, URL_PERFIL
    FROM FIDE_RED_SOCIAL_TB
    WHERE ID_EMPRENDIMIENTO = p_id_emprendimiento
    ORDER BY ID_RED_SOCIAL;
END$$

-- ============================================================
-- 4. CATEGORÍA
-- ============================================================

-- Categorías con cantidad de productos activos y de emprendimientos (marcas)
DROP PROCEDURE IF EXISTS sp_categoria_listar$$
CREATE PROCEDURE sp_categoria_listar()
BEGIN
    SELECT c.ID_CATEGORIA AS id_categoria, c.NOMBRE_CATEGORIA AS nombre, c.ICONO AS icono,
           c.DESCRIPCION AS descripcion, c.ES_ARTESANAL AS artesanal,
           COUNT(p.id_producto) AS productos,
           COUNT(DISTINCT p.id_emprendimiento) AS marcas
    FROM FIDE_CATEGORIA_TB c
    LEFT JOIN FIDE_PRODUCTO_V p ON p.id_categoria = c.ID_CATEGORIA AND p.estado <> 'inactivo'
    GROUP BY c.ID_CATEGORIA, c.NOMBRE_CATEGORIA, c.ICONO, c.DESCRIPCION, c.ES_ARTESANAL
    ORDER BY c.ID_CATEGORIA;
END$$

-- Una categoría por id (con su cantidad de productos, para no borrar si tiene)
DROP PROCEDURE IF EXISTS sp_categoria_obtener$$
CREATE PROCEDURE sp_categoria_obtener(IN p_id_categoria INT)
BEGIN
    SELECT c.ID_CATEGORIA AS id_categoria, c.NOMBRE_CATEGORIA AS nombre, c.ICONO AS icono,
           c.DESCRIPCION AS descripcion, c.ES_ARTESANAL AS artesanal,
           (SELECT COUNT(*) FROM FIDE_PRODUCTO_TB p WHERE p.ID_CATEGORIA = c.ID_CATEGORIA) AS productos
    FROM FIDE_CATEGORIA_TB c
    WHERE c.ID_CATEGORIA = p_id_categoria;
END$$

-- Busca una categoría por nombre (para no repetir nombres)
DROP PROCEDURE IF EXISTS sp_categoria_obtener_por_nombre$$
CREATE PROCEDURE sp_categoria_obtener_por_nombre(IN p_nombre VARCHAR(60))
BEGIN
    SELECT ID_CATEGORIA AS id_categoria, NOMBRE_CATEGORIA AS nombre
    FROM FIDE_CATEGORIA_TB
    WHERE NOMBRE_CATEGORIA = p_nombre;
END$$

DROP PROCEDURE IF EXISTS sp_categoria_insertar$$
CREATE PROCEDURE sp_categoria_insertar(
    IN p_nombre VARCHAR(60),
    IN p_icono VARCHAR(40),
    IN p_descripcion VARCHAR(255),
    IN p_artesanal TINYINT
)
BEGIN
    INSERT INTO FIDE_CATEGORIA_TB (NOMBRE_CATEGORIA, ICONO, DESCRIPCION, ES_ARTESANAL)
    VALUES (p_nombre, p_icono, p_descripcion, p_artesanal);

    SELECT LAST_INSERT_ID() AS id;
END$$

DROP PROCEDURE IF EXISTS sp_categoria_actualizar$$
CREATE PROCEDURE sp_categoria_actualizar(
    IN p_id_categoria INT,
    IN p_nombre VARCHAR(60),
    IN p_descripcion VARCHAR(255),
    IN p_artesanal TINYINT
)
BEGIN
    UPDATE FIDE_CATEGORIA_TB
    SET NOMBRE_CATEGORIA = p_nombre, DESCRIPCION = p_descripcion, ES_ARTESANAL = p_artesanal
    WHERE ID_CATEGORIA = p_id_categoria;
END$$

-- Solo se llama cuando la categoría no tiene productos (lo revisa CategoriaService)
DROP PROCEDURE IF EXISTS sp_categoria_eliminar$$
CREATE PROCEDURE sp_categoria_eliminar(IN p_id_categoria INT)
BEGIN
    DELETE FROM FIDE_CATEGORIA_TB WHERE ID_CATEGORIA = p_id_categoria;
END$$

-- ============================================================
-- 5. PRODUCTO E IMÁGENES
-- ============================================================

-- Catálogo público con filtros. Los parámetros vacíos ('' o 0) no filtran.
--   p_categorias: ids separados por coma. Ej: '1,3'
--   p_pago: sinpe | tarjeta | efectivo
--   p_orden: recomendados | recientes | precio_asc | precio_desc
DROP PROCEDURE IF EXISTS sp_producto_buscar$$
CREATE PROCEDURE sp_producto_buscar(
    IN p_texto VARCHAR(100),
    IN p_provincia VARCHAR(20),
    IN p_categorias VARCHAR(100),
    IN p_minimo DECIMAL(10, 2),
    IN p_maximo DECIMAL(10, 2),
    IN p_pago VARCHAR(10),
    IN p_orden VARCHAR(20),
    IN p_limite INT,
    IN p_desde INT
)
BEGIN
    SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.cantidad_disponible, p.estado,
           p.id_categoria, p.categoria, p.imagen, p.calificacion,
           e.id_emprendimiento, e.nombre_negocio, e.ubicacion, e.metodos_pago
    FROM FIDE_PRODUCTO_V p
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo'
      AND e.estado_verificacion = 'aprobado'
      AND u.estado = 'activo'
      AND (p_texto = '' OR p.nombre LIKE CONCAT('%', p_texto, '%')
                        OR e.nombre_negocio LIKE CONCAT('%', p_texto, '%')
                        OR e.ubicacion LIKE CONCAT('%', p_texto, '%')
                        OR p.categoria LIKE CONCAT('%', p_texto, '%'))
      AND (p_provincia = '' OR e.provincia = p_provincia)
      AND (p_categorias = '' OR FIND_IN_SET(p.id_categoria, p_categorias) > 0)
      AND (p_minimo = 0 OR p.precio >= p_minimo)
      AND (p_maximo = 0 OR p.precio <= p_maximo)
      AND (p_pago = '' OR FIND_IN_SET(p_pago, e.metodos_pago) > 0)
    ORDER BY
        CASE WHEN p_orden = 'precio_asc' THEN p.precio END ASC,
        CASE WHEN p_orden = 'precio_desc' THEN p.precio END DESC,
        CASE WHEN p_orden = 'recientes' THEN p.fecha_publicacion END DESC,
        p.calificacion DESC,
        p.id_producto
    LIMIT p_limite OFFSET p_desde;
END$$

-- Cuántos productos cumplen los filtros (para "8 de 34 productos" y la paginación)
DROP PROCEDURE IF EXISTS sp_producto_contar_busqueda$$
CREATE PROCEDURE sp_producto_contar_busqueda(
    IN p_texto VARCHAR(100),
    IN p_provincia VARCHAR(20),
    IN p_categorias VARCHAR(100),
    IN p_minimo DECIMAL(10, 2),
    IN p_maximo DECIMAL(10, 2),
    IN p_pago VARCHAR(10)
)
BEGIN
    SELECT COUNT(*) AS total
    FROM FIDE_PRODUCTO_V p
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo'
      AND e.estado_verificacion = 'aprobado'
      AND u.estado = 'activo'
      AND (p_texto = '' OR p.nombre LIKE CONCAT('%', p_texto, '%')
                        OR e.nombre_negocio LIKE CONCAT('%', p_texto, '%')
                        OR e.ubicacion LIKE CONCAT('%', p_texto, '%')
                        OR p.categoria LIKE CONCAT('%', p_texto, '%'))
      AND (p_provincia = '' OR e.provincia = p_provincia)
      AND (p_categorias = '' OR FIND_IN_SET(p.id_categoria, p_categorias) > 0)
      AND (p_minimo = 0 OR p.precio >= p_minimo)
      AND (p_maximo = 0 OR p.precio <= p_maximo)
      AND (p_pago = '' OR FIND_IN_SET(p_pago, e.metodos_pago) > 0);
END$$

-- Productos de un emprendimiento. p_solo_activos = 1 para la vitrina pública.
DROP PROCEDURE IF EXISTS sp_producto_listar_por_emprendimiento$$
CREATE PROCEDURE sp_producto_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_solo_activos TINYINT)
BEGIN
    SELECT id_producto, nombre, descripcion, precio, cantidad_disponible, estado,
           id_categoria, categoria, imagen
    FROM FIDE_PRODUCTO_V
    WHERE id_emprendimiento = p_id_emprendimiento
      AND (p_solo_activos = 0 OR estado = 'activo')
    ORDER BY id_producto DESC;
END$$

-- Un producto con su emprendimiento
DROP PROCEDURE IF EXISTS sp_producto_obtener$$
CREATE PROCEDURE sp_producto_obtener(IN p_id_producto INT)
BEGIN
    SELECT p.id_producto, p.id_emprendimiento, p.id_categoria, p.nombre, p.descripcion,
           p.precio, p.cantidad_disponible, p.estado, p.categoria, p.imagen,
           e.nombre_negocio, e.estado_verificacion
    FROM FIDE_PRODUCTO_V p
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    WHERE p.id_producto = p_id_producto;
END$$

-- Productos del carrito (ids separados por coma) con los datos del emprendimiento
DROP PROCEDURE IF EXISTS sp_producto_listar_por_ids$$
CREATE PROCEDURE sp_producto_listar_por_ids(IN p_ids VARCHAR(500))
BEGIN
    SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.cantidad_disponible, p.estado,
           p.categoria, p.imagen,
           e.id_emprendimiento, e.nombre_negocio, e.ubicacion, e.logo, e.metodos_pago,
           u.nombre AS dueno, u.telefono
    FROM FIDE_PRODUCTO_V p
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE FIND_IN_SET(p.id_producto, p_ids) > 0
    ORDER BY e.id_emprendimiento, p.id_producto;
END$$

-- Sugerencias para el carrito: productos activos mejor calificados
DROP PROCEDURE IF EXISTS sp_producto_recomendados$$
CREATE PROCEDURE sp_producto_recomendados(IN p_limite INT, IN p_excluir VARCHAR(500))
BEGIN
    SELECT p.id_producto, p.nombre, p.precio, p.imagen, p.calificacion, e.nombre_negocio, e.ubicacion
    FROM FIDE_PRODUCTO_V p
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo' AND e.estado_verificacion = 'aprobado' AND u.estado = 'activo'
      AND (p_excluir = '' OR FIND_IN_SET(p.id_producto, p_excluir) = 0)
    ORDER BY p.calificacion DESC, p.id_producto
    LIMIT p_limite;
END$$

DROP PROCEDURE IF EXISTS sp_producto_insertar$$
CREATE PROCEDURE sp_producto_insertar(
    IN p_id_emprendimiento INT,
    IN p_id_categoria INT,
    IN p_nombre VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_precio DECIMAL(10, 2),
    IN p_cantidad INT
)
BEGIN
    INSERT INTO FIDE_PRODUCTO_TB (ID_EMPRENDIMIENTO, ID_CATEGORIA, NOMBRE_PRODUCTO, DESCRIPCION, PRECIO, CANTIDAD_DISPONIBLE, ID_ESTADO)
    VALUES (p_id_emprendimiento, p_id_categoria, p_nombre, p_descripcion, p_precio, p_cantidad,
            (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = IF(p_cantidad = 0, 'agotado', 'activo')));

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Solo actualiza si el producto es de ese emprendimiento (seguridad).
-- Si estaba pausado (inactivo) sigue pausado; si no, queda activo o agotado según la cantidad.
DROP PROCEDURE IF EXISTS sp_producto_actualizar$$
CREATE PROCEDURE sp_producto_actualizar(
    IN p_id_producto INT,
    IN p_id_emprendimiento INT,
    IN p_id_categoria INT,
    IN p_nombre VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_precio DECIMAL(10, 2),
    IN p_cantidad INT
)
BEGIN
    DECLARE v_estado VARCHAR(20) DEFAULT '';

    SELECT estado INTO v_estado FROM FIDE_PRODUCTO_V WHERE id_producto = p_id_producto;

    IF v_estado <> 'inactivo' THEN
        SET v_estado = IF(p_cantidad = 0, 'agotado', 'activo');
    END IF;

    UPDATE FIDE_PRODUCTO_TB
    SET ID_CATEGORIA        = p_id_categoria,
        NOMBRE_PRODUCTO     = p_nombre,
        DESCRIPCION         = p_descripcion,
        PRECIO              = p_precio,
        CANTIDAD_DISPONIBLE = p_cantidad,
        ID_ESTADO           = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = v_estado)
    WHERE ID_PRODUCTO = p_id_producto AND ID_EMPRENDIMIENTO = p_id_emprendimiento;
END$$

-- Pausa o activa un producto (interruptor de Mis Productos)
DROP PROCEDURE IF EXISTS sp_producto_cambiar_estado$$
CREATE PROCEDURE sp_producto_cambiar_estado(
    IN p_id_producto INT,
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(20)
)
BEGIN
    UPDATE FIDE_PRODUCTO_TB
    SET ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = p_estado)
    WHERE ID_PRODUCTO = p_id_producto AND ID_EMPRENDIMIENTO = p_id_emprendimiento;
END$$

-- Guarda la foto principal del producto (reemplaza la anterior)
DROP PROCEDURE IF EXISTS sp_imagen_producto_guardar$$
CREATE PROCEDURE sp_imagen_producto_guardar(IN p_id_producto INT, IN p_url VARCHAR(255))
BEGIN
    DELETE FROM FIDE_IMAGEN_PRODUCTO_TB WHERE ID_PRODUCTO = p_id_producto AND ORDEN = 1;
    INSERT INTO FIDE_IMAGEN_PRODUCTO_TB (ID_PRODUCTO, URL_IMAGEN, ORDEN) VALUES (p_id_producto, p_url, 1);
END$$

-- ============================================================
-- 6. RESEÑAS Y FAVORITOS
-- ============================================================

-- Reseñas de todos los productos de un emprendimiento (vitrina)
DROP PROCEDURE IF EXISTS sp_resena_listar_por_emprendimiento$$
CREATE PROCEDURE sp_resena_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_limite INT)
BEGIN
    SELECT r.ID_RESENA AS id_resena, r.CALIFICACION AS calificacion, r.COMENTARIO AS comentario,
           r.FECHA_RESENA AS fecha, u.nombre AS autor, u.provincia, p.NOMBRE_PRODUCTO AS producto
    FROM FIDE_RESENA_TB r
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = r.ID_PRODUCTO
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = r.ID_USUARIO
    WHERE p.ID_EMPRENDIMIENTO = p_id_emprendimiento
    ORDER BY r.FECHA_RESENA DESC
    LIMIT p_limite;
END$$

DROP PROCEDURE IF EXISTS sp_favorito_listar$$
CREATE PROCEDURE sp_favorito_listar(IN p_id_usuario INT)
BEGIN
    SELECT p.id_producto, p.nombre, p.precio, p.imagen, e.id_emprendimiento, e.nombre_negocio, e.ubicacion
    FROM FIDE_FAVORITO_TB f
    INNER JOIN FIDE_PRODUCTO_V p ON p.id_producto = f.ID_PRODUCTO
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    WHERE f.ID_USUARIO = p_id_usuario
    ORDER BY f.FECHA_AGREGADO DESC;
END$$

-- Agrega un favorito (si ya existía no hace nada)
DROP PROCEDURE IF EXISTS sp_favorito_agregar$$
CREATE PROCEDURE sp_favorito_agregar(IN p_id_usuario INT, IN p_id_producto INT)
BEGIN
    INSERT IGNORE INTO FIDE_FAVORITO_TB (ID_USUARIO, ID_PRODUCTO) VALUES (p_id_usuario, p_id_producto);
END$$

DROP PROCEDURE IF EXISTS sp_favorito_eliminar$$
CREATE PROCEDURE sp_favorito_eliminar(IN p_id_usuario INT, IN p_id_producto INT)
BEGIN
    DELETE FROM FIDE_FAVORITO_TB WHERE ID_USUARIO = p_id_usuario AND ID_PRODUCTO = p_id_producto;
END$$

-- ============================================================
-- 7. CARRITO (solo para usuarios con sesión; sin sesión vive en $_SESSION)
-- ============================================================

-- Productos y cantidades del carrito guardado de un usuario
DROP PROCEDURE IF EXISTS sp_carrito_listar$$
CREATE PROCEDURE sp_carrito_listar(IN p_id_usuario INT)
BEGIN
    SELECT cd.ID_PRODUCTO AS id_producto, cd.CANTIDAD AS cantidad
    FROM FIDE_CARRITO_TB c
    INNER JOIN FIDE_CARRITO_DETALLE_TB cd ON cd.ID_CARRITO = c.ID_CARRITO
    WHERE c.ID_USUARIO = p_id_usuario
    ORDER BY cd.FECHA_AGREGADO;
END$$

-- Pone la cantidad de un producto en el carrito (0 lo quita). Crea el carrito si no existe.
DROP PROCEDURE IF EXISTS sp_carrito_guardar_producto$$
CREATE PROCEDURE sp_carrito_guardar_producto(IN p_id_usuario INT, IN p_id_producto INT, IN p_cantidad INT)
BEGIN
    DECLARE v_id_carrito INT DEFAULT NULL;

    SELECT ID_CARRITO INTO v_id_carrito FROM FIDE_CARRITO_TB WHERE ID_USUARIO = p_id_usuario;

    IF v_id_carrito IS NULL THEN
        INSERT INTO FIDE_CARRITO_TB (ID_USUARIO) VALUES (p_id_usuario);
        SET v_id_carrito = LAST_INSERT_ID();
    END IF;

    IF p_cantidad <= 0 THEN
        DELETE FROM FIDE_CARRITO_DETALLE_TB WHERE ID_CARRITO = v_id_carrito AND ID_PRODUCTO = p_id_producto;
    ELSE
        INSERT INTO FIDE_CARRITO_DETALLE_TB (ID_CARRITO, ID_PRODUCTO, CANTIDAD)
        VALUES (v_id_carrito, p_id_producto, p_cantidad)
        ON DUPLICATE KEY UPDATE CANTIDAD = p_cantidad;
    END IF;

    UPDATE FIDE_CARRITO_TB SET FECHA_ACTUALIZACION = NOW() WHERE ID_CARRITO = v_id_carrito;
END$$

-- Vacía el carrito guardado (después de confirmar el pedido)
DROP PROCEDURE IF EXISTS sp_carrito_vaciar$$
CREATE PROCEDURE sp_carrito_vaciar(IN p_id_usuario INT)
BEGIN
    DELETE cd FROM FIDE_CARRITO_DETALLE_TB cd
    INNER JOIN FIDE_CARRITO_TB c ON c.ID_CARRITO = cd.ID_CARRITO
    WHERE c.ID_USUARIO = p_id_usuario;
END$$

-- ============================================================
-- 8. PEDIDOS
-- ============================================================

-- Encabezado del pedido (queda pendiente). p_id_direccion sale de sp_direccion_insertar.
DROP PROCEDURE IF EXISTS sp_pedido_insertar$$
CREATE PROCEDURE sp_pedido_insertar(
    IN p_id_usuario INT,
    IN p_id_direccion INT,
    IN p_metodo_pago VARCHAR(20),
    IN p_nombre_receptor VARCHAR(100),
    IN p_telefono_receptor VARCHAR(15),
    IN p_total DECIMAL(10, 2)
)
BEGIN
    INSERT INTO FIDE_PEDIDO_TB (ID_USUARIO, ID_DIRECCION, ID_METODO_PAGO, NOMBRE_RECEPTOR, TELEFONO_RECEPTOR, TOTAL, ID_ESTADO)
    VALUES (p_id_usuario, p_id_direccion,
            (SELECT ID_METODO_PAGO FROM FIDE_METODO_PAGO_TB WHERE NOMBRE_METODO = p_metodo_pago),
            p_nombre_receptor, p_telefono_receptor, p_total,
            (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'pendiente'));

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Una línea del pedido (pendiente). Rebaja el inventario y marca agotado si llega a 0.
DROP PROCEDURE IF EXISTS sp_detalle_pedido_insertar$$
CREATE PROCEDURE sp_detalle_pedido_insertar(
    IN p_id_pedido INT,
    IN p_id_producto INT,
    IN p_cantidad INT,
    IN p_precio DECIMAL(10, 2)
)
BEGIN
    INSERT INTO FIDE_DETALLE_PEDIDO_TB (ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL, ID_ESTADO)
    VALUES (p_id_pedido, p_id_producto, p_cantidad, p_precio, p_cantidad * p_precio,
            (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'pendiente'));

    UPDATE FIDE_PRODUCTO_TB
    SET CANTIDAD_DISPONIBLE = GREATEST(CANTIDAD_DISPONIBLE - p_cantidad, 0)
    WHERE ID_PRODUCTO = p_id_producto;

    UPDATE FIDE_PRODUCTO_TB
    SET ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'agotado')
    WHERE ID_PRODUCTO = p_id_producto
      AND CANTIDAD_DISPONIBLE = 0
      AND ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = 'activo');
END$$

-- Encabezado y dirección de un pedido. p_id_usuario = 0 no filtra por dueño (admin).
DROP PROCEDURE IF EXISTS sp_pedido_obtener$$
CREATE PROCEDURE sp_pedido_obtener(IN p_id_pedido INT, IN p_id_usuario INT)
BEGIN
    SELECT pe.ID_PEDIDO AS id_pedido, pe.ID_USUARIO AS id_usuario, pe.FECHA_PEDIDO AS fecha_pedido,
           m.NOMBRE_METODO AS metodo_pago, pe.TOTAL AS total, es.NOMBRE_ESTADO AS estado,
           pe.NOMBRE_RECEPTOR AS nombre_receptor, pe.TELEFONO_RECEPTOR AS telefono,
           d.provincia, d.canton, d.distrito, d.otras_senas AS direccion_exacta, d.codigo_postal,
           u.nombre AS cliente, u.correo
    FROM FIDE_PEDIDO_TB pe
    INNER JOIN FIDE_METODO_PAGO_TB m ON m.ID_METODO_PAGO = pe.ID_METODO_PAGO
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = pe.ID_ESTADO
    INNER JOIN FIDE_DIRECCION_V d ON d.id_direccion = pe.ID_DIRECCION
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = pe.ID_USUARIO
    WHERE pe.ID_PEDIDO = p_id_pedido
      AND (p_id_usuario = 0 OR pe.ID_USUARIO = p_id_usuario);
END$$

-- Líneas de un pedido con el emprendimiento de cada producto (para agrupar por SINPE)
DROP PROCEDURE IF EXISTS sp_detalle_pedido_listar$$
CREATE PROCEDURE sp_detalle_pedido_listar(IN p_id_pedido INT)
BEGIN
    SELECT dp.ID_DETALLE AS id_detalle, dp.ID_PRODUCTO AS id_producto, dp.CANTIDAD AS cantidad,
           dp.PRECIO_UNITARIO AS precio_unitario, dp.SUBTOTAL AS subtotal, es.NOMBRE_ESTADO AS estado_entrega,
           p.nombre, p.imagen, e.id_emprendimiento, e.nombre_negocio, u.nombre AS dueno, u.telefono
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PRODUCTO_V p ON p.id_producto = dp.ID_PRODUCTO
    INNER JOIN FIDE_EMPRENDIMIENTO_V e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
    WHERE dp.ID_PEDIDO = p_id_pedido
    ORDER BY e.id_emprendimiento, dp.ID_DETALLE;
END$$

-- Pedidos de un cliente (Mi perfil > Mis pedidos), una fila por pedido
DROP PROCEDURE IF EXISTS sp_pedido_listar_por_cliente$$
CREATE PROCEDURE sp_pedido_listar_por_cliente(IN p_id_usuario INT)
BEGIN
    -- La consulta de adentro agrupa por pedido; la de afuera agrega la foto del primer producto
    SELECT t.*,
           (SELECT i.URL_IMAGEN FROM FIDE_IMAGEN_PRODUCTO_TB i
             WHERE i.ID_PRODUCTO = t.primer_producto ORDER BY i.ORDEN LIMIT 1) AS imagen
    FROM (
    SELECT pe.ID_PEDIDO AS id_pedido, pe.FECHA_PEDIDO AS fecha_pedido, m.NOMBRE_METODO AS metodo_pago,
           pe.TOTAL AS total,
           COUNT(dp.ID_DETALLE) AS lineas,
           SUM(dp.CANTIDAD) AS unidades,
           GROUP_CONCAT(DISTINCT e.NOMBRE_NEGOCIO ORDER BY e.NOMBRE_NEGOCIO SEPARATOR ', ') AS emprendimientos,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'entregado' THEN 1 ELSE 0 END) AS entregados,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'enviado' THEN 1 ELSE 0 END) AS enviados,
           MIN(dp.ID_PRODUCTO) AS primer_producto
    FROM FIDE_PEDIDO_TB pe
    INNER JOIN FIDE_METODO_PAGO_TB m ON m.ID_METODO_PAGO = pe.ID_METODO_PAGO
    INNER JOIN FIDE_DETALLE_PEDIDO_TB dp ON dp.ID_PEDIDO = pe.ID_PEDIDO
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    INNER JOIN FIDE_EMPRENDIMIENTO_TB e ON e.ID_EMPRENDIMIENTO = p.ID_EMPRENDIMIENTO
    WHERE pe.ID_USUARIO = p_id_usuario
    GROUP BY pe.ID_PEDIDO, pe.FECHA_PEDIDO, m.NOMBRE_METODO, pe.TOTAL
    ) t
    ORDER BY t.fecha_pedido DESC;
END$$

-- Pedidos que recibió un emprendimiento: solo sus productos, una fila por pedido
DROP PROCEDURE IF EXISTS sp_pedido_listar_por_emprendimiento$$
CREATE PROCEDURE sp_pedido_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_limite INT)
BEGIN
    SELECT t.*,
           (SELECT i.URL_IMAGEN FROM FIDE_IMAGEN_PRODUCTO_TB i
             WHERE i.ID_PRODUCTO = t.primer_producto ORDER BY i.ORDEN LIMIT 1) AS imagen
    FROM (
    SELECT pe.ID_PEDIDO AS id_pedido, pe.FECHA_PEDIDO AS fecha_pedido, m.NOMBRE_METODO AS metodo_pago,
           pe.NOMBRE_RECEPTOR AS cliente, pe.TELEFONO_RECEPTOR AS telefono_cliente,
           d.canton, d.provincia, d.otras_senas AS direccion_exacta,
           SUM(dp.SUBTOTAL) AS total,
           SUM(dp.CANTIDAD) AS unidades,
           COUNT(dp.ID_DETALLE) AS lineas,
           GROUP_CONCAT(CONCAT(dp.CANTIDAD, '× ', p.NOMBRE_PRODUCTO) ORDER BY dp.ID_DETALLE SEPARATOR ' · ') AS productos,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
           SUM(CASE WHEN es.NOMBRE_ESTADO IN ('confirmado', 'enviado') THEN 1 ELSE 0 END) AS confirmados,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'entregado' THEN 1 ELSE 0 END) AS entregados,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
           MIN(dp.ID_PRODUCTO) AS primer_producto
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_METODO_PAGO_TB m ON m.ID_METODO_PAGO = pe.ID_METODO_PAGO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    INNER JOIN FIDE_DIRECCION_V d ON d.id_direccion = pe.ID_DIRECCION
    WHERE p.ID_EMPRENDIMIENTO = p_id_emprendimiento
    GROUP BY pe.ID_PEDIDO, pe.FECHA_PEDIDO, m.NOMBRE_METODO, pe.NOMBRE_RECEPTOR, pe.TELEFONO_RECEPTOR,
             d.canton, d.provincia, d.otras_senas
    ) t
    ORDER BY t.fecha_pedido DESC
    LIMIT p_limite;
END$$

-- Cambia el estado de las líneas de un emprendimiento dentro de un pedido y
-- actualiza el estado general del pedido (entregado cuando ya no queda nada abierto).
DROP PROCEDURE IF EXISTS sp_detalle_pedido_cambiar_estado$$
CREATE PROCEDURE sp_detalle_pedido_cambiar_estado(
    IN p_id_pedido INT,
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(20)
)
BEGIN
    DECLARE v_filas INT DEFAULT 0;
    DECLARE v_abiertas INT DEFAULT 0;

    UPDATE FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    SET dp.ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO = p_estado)
    WHERE dp.ID_PEDIDO = p_id_pedido
      AND p.ID_EMPRENDIMIENTO = p_id_emprendimiento
      AND dp.ID_ESTADO NOT IN (SELECT ID_ESTADO FROM FIDE_ESTADO_TB WHERE NOMBRE_ESTADO IN ('entregado', 'rechazado'));
    SET v_filas = ROW_COUNT();

    SELECT COUNT(*) INTO v_abiertas
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    WHERE dp.ID_PEDIDO = p_id_pedido AND es.NOMBRE_ESTADO NOT IN ('entregado', 'rechazado');

    UPDATE FIDE_PEDIDO_TB
    SET ID_ESTADO = (SELECT ID_ESTADO FROM FIDE_ESTADO_TB
                     WHERE NOMBRE_ESTADO = IF(v_abiertas = 0, 'entregado', 'confirmado'))
    WHERE ID_PEDIDO = p_id_pedido AND v_filas > 0;

    SELECT v_filas AS filas;
END$$

-- ============================================================
-- 9. REPORTES
--    No cuentan las líneas rechazadas. Fechas en formato 'AAAA-MM-DD'.
-- ============================================================

-- Ventas por día de un emprendimiento (PHP las agrupa por semana o mes)
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_ventas_diarias$$
CREATE PROCEDURE sp_reporte_vendedor_ventas_diarias(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT DATE(pe.FECHA_PEDIDO) AS fecha, SUM(dp.SUBTOTAL) AS monto
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    WHERE p.ID_EMPRENDIMIENTO = p_id_emprendimiento
      AND es.NOMBRE_ESTADO <> 'rechazado'
      AND DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta
    GROUP BY DATE(pe.FECHA_PEDIDO)
    ORDER BY fecha;
END$$

-- Totales del período: ventas, pedidos y % entregado
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_resumen$$
CREATE PROCEDURE sp_reporte_vendedor_resumen(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT COALESCE(SUM(dp.SUBTOTAL), 0) AS ventas,
           COUNT(DISTINCT dp.ID_PEDIDO) AS pedidos,
           COUNT(dp.ID_DETALLE) AS lineas,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'entregado' THEN 1 ELSE 0 END) AS entregadas,
           SUM(CASE WHEN es.NOMBRE_ESTADO = 'pendiente' THEN 1 ELSE 0 END) AS pendientes
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    WHERE p.ID_EMPRENDIMIENTO = p_id_emprendimiento
      AND es.NOMBRE_ESTADO <> 'rechazado'
      AND DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta;
END$$

-- Producto más vendido del período
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_producto_top$$
CREATE PROCEDURE sp_reporte_vendedor_producto_top(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT p.NOMBRE_PRODUCTO AS nombre, SUM(dp.CANTIDAD) AS unidades
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    WHERE p.ID_EMPRENDIMIENTO = p_id_emprendimiento
      AND es.NOMBRE_ESTADO <> 'rechazado'
      AND DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta
    GROUP BY p.ID_PRODUCTO, p.NOMBRE_PRODUCTO
    ORDER BY unidades DESC
    LIMIT 1;
END$$

-- Números del inicio del portal del vendedor
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_inventario$$
CREATE PROCEDURE sp_reporte_vendedor_inventario(IN p_id_emprendimiento INT)
BEGIN
    SELECT SUM(CASE WHEN estado <> 'inactivo' THEN 1 ELSE 0 END) AS catalogo,
           SUM(CASE WHEN estado <> 'inactivo' AND cantidad_disponible <= 5 THEN 1 ELSE 0 END) AS stock_bajo
    FROM FIDE_PRODUCTO_V
    WHERE id_emprendimiento = p_id_emprendimiento;
END$$

-- Totales generales de la plataforma para un período (admin)
DROP PROCEDURE IF EXISTS sp_reporte_admin_resumen$$
CREATE PROCEDURE sp_reporte_admin_resumen(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT COALESCE(SUM(dp.SUBTOTAL), 0) AS ventas,
           COUNT(DISTINCT dp.ID_PEDIDO) AS pedidos,
           COUNT(DISTINCT CASE WHEN m.NOMBRE_METODO = 'sinpe' THEN pe.ID_PEDIDO END) AS pedidos_sinpe
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_METODO_PAGO_TB m ON m.ID_METODO_PAGO = pe.ID_METODO_PAGO
    WHERE es.NOMBRE_ESTADO <> 'rechazado'
      AND DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta;
END$$

-- Ventas por categoría en el período (barras del reporte del admin)
DROP PROCEDURE IF EXISTS sp_reporte_admin_ventas_categoria$$
CREATE PROCEDURE sp_reporte_admin_ventas_categoria(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT c.NOMBRE_CATEGORIA AS categoria, SUM(dp.SUBTOTAL) AS monto
    FROM FIDE_DETALLE_PEDIDO_TB dp
    INNER JOIN FIDE_ESTADO_TB es ON es.ID_ESTADO = dp.ID_ESTADO
    INNER JOIN FIDE_PEDIDO_TB pe ON pe.ID_PEDIDO = dp.ID_PEDIDO
    INNER JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = dp.ID_PRODUCTO
    INNER JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA
    WHERE es.NOMBRE_ESTADO <> 'rechazado'
      AND DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta
    GROUP BY c.ID_CATEGORIA, c.NOMBRE_CATEGORIA
    ORDER BY monto DESC
    LIMIT 6;
END$$

-- Pedidos por provincia de entrega en el período (cobertura territorial)
DROP PROCEDURE IF EXISTS sp_reporte_admin_ventas_provincia$$
CREATE PROCEDURE sp_reporte_admin_ventas_provincia(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT d.provincia, COUNT(*) AS pedidos
    FROM FIDE_PEDIDO_TB pe
    INNER JOIN FIDE_DIRECCION_V d ON d.id_direccion = pe.ID_DIRECCION
    WHERE DATE(pe.FECHA_PEDIDO) BETWEEN p_desde AND p_hasta
    GROUP BY d.provincia
    ORDER BY pedidos DESC;
END$$

-- Números del inicio del panel del administrador
DROP PROCEDURE IF EXISTS sp_reporte_admin_inicio$$
CREATE PROCEDURE sp_reporte_admin_inicio()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM FIDE_EMPRENDIMIENTO_V WHERE estado_verificacion = 'pendiente') AS pendientes,
        (SELECT COUNT(*) FROM FIDE_EMPRENDIMIENTO_V e INNER JOIN FIDE_USUARIO_V u ON u.id_usuario = e.id_usuario
          WHERE e.estado_verificacion = 'aprobado' AND u.estado = 'activo') AS activos,
        (SELECT COUNT(*) FROM FIDE_EMPRENDIMIENTO_V
          WHERE estado_verificacion = 'aprobado'
            AND YEAR(COALESCE(fecha_verificacion, fecha_solicitud)) = YEAR(CURDATE())
            AND MONTH(COALESCE(fecha_verificacion, fecha_solicitud)) = MONTH(CURDATE())) AS nuevos_mes,
        (SELECT COUNT(*) FROM FIDE_USUARIO_V WHERE nombre_rol <> 'Administrador') AS usuarios,
        (SELECT COUNT(*) FROM FIDE_CATEGORIA_TB) AS categorias,
        (SELECT COUNT(*) FROM FIDE_CATEGORIA_TB WHERE ES_ARTESANAL = 1) AS categorias_artesanales;
END$$

DELIMITER ;
