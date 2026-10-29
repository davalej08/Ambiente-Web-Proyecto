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
--  Se corre después de 01_tablas.sql:
--    php database/instalar.php   (corre los 3 scripts en orden)
--  o en MySQL Workbench: abrir el archivo > ejecutar todo.
--
--  Secciones:
--    1. Rol y usuario
--    2. Emprendimiento
--    3. Categoría
--    4. Producto e imágenes
--    5. Reseñas y favoritos
--    6. Pedidos
--    7. Reportes
-- ============================================================

DELIMITER $$

USE ventica$$

-- ============================================================
-- 1. ROL Y USUARIO
-- ============================================================

-- Busca un rol por su nombre (Cliente, Emprendedor, Administrador)
DROP PROCEDURE IF EXISTS sp_rol_obtener_por_nombre$$
CREATE PROCEDURE sp_rol_obtener_por_nombre(IN p_nombre VARCHAR(30))
BEGIN
    SELECT id_rol, nombre_rol
    FROM rol
    WHERE nombre_rol = p_nombre;
END$$

-- Usuario por correo (para el login). Incluye su rol y su emprendimiento si tiene.
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_correo$$
CREATE PROCEDURE sp_usuario_obtener_por_correo(IN p_correo VARCHAR(150))
BEGIN
    SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre, u.correo, u.contrasena,
           u.telefono, u.provincia, u.direccion, u.fecha_registro, u.estado,
           e.id_emprendimiento, e.nombre_negocio, e.estado_verificacion
    FROM usuario u
    INNER JOIN rol r ON r.id_rol = u.id_rol
    LEFT JOIN emprendimiento e ON e.id_usuario = u.id_usuario
    WHERE u.correo = p_correo;
END$$

-- Usuario por id
DROP PROCEDURE IF EXISTS sp_usuario_obtener$$
CREATE PROCEDURE sp_usuario_obtener(IN p_id_usuario INT)
BEGIN
    SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre, u.correo, u.contrasena,
           u.telefono, u.provincia, u.direccion, u.fecha_registro, u.estado,
           e.id_emprendimiento, e.nombre_negocio, e.estado_verificacion
    FROM usuario u
    INNER JOIN rol r ON r.id_rol = u.id_rol
    LEFT JOIN emprendimiento e ON e.id_usuario = u.id_usuario
    WHERE u.id_usuario = p_id_usuario;
END$$

-- Crea un usuario y devuelve su id
DROP PROCEDURE IF EXISTS sp_usuario_insertar$$
CREATE PROCEDURE sp_usuario_insertar(
    IN p_id_rol INT,
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(255),
    IN p_telefono VARCHAR(15)
)
BEGIN
    INSERT INTO usuario (id_rol, nombre, correo, contrasena, telefono)
    VALUES (p_id_rol, p_nombre, p_correo, p_contrasena, p_telefono);

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Actualiza los datos personales y la dirección predeterminada (Mi perfil)
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_perfil$$
CREATE PROCEDURE sp_usuario_actualizar_perfil(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150),
    IN p_telefono VARCHAR(15),
    IN p_provincia VARCHAR(20),
    IN p_direccion VARCHAR(255)
)
BEGIN
    UPDATE usuario
    SET nombre    = p_nombre,
        correo    = p_correo,
        telefono  = p_telefono,
        provincia = NULLIF(p_provincia, ''),
        direccion = NULLIF(p_direccion, '')
    WHERE id_usuario = p_id_usuario;
END$$

-- Cambia la contraseña (ya viene cifrada desde PHP)
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_contrasena$$
CREATE PROCEDURE sp_usuario_actualizar_contrasena(IN p_id_usuario INT, IN p_contrasena VARCHAR(255))
BEGIN
    UPDATE usuario SET contrasena = p_contrasena WHERE id_usuario = p_id_usuario;
END$$

-- Activa, inactiva o bloquea (suspende) una cuenta. No se borra nada.
DROP PROCEDURE IF EXISTS sp_usuario_cambiar_estado$$
CREATE PROCEDURE sp_usuario_cambiar_estado(IN p_id_usuario INT, IN p_estado VARCHAR(10))
BEGIN
    UPDATE usuario SET estado = p_estado WHERE id_usuario = p_id_usuario;
END$$

-- Datos que el administrador puede editar de una cuenta
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_admin$$
CREATE PROCEDURE sp_usuario_actualizar_admin(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150)
)
BEGIN
    UPDATE usuario SET nombre = p_nombre, correo = p_correo WHERE id_usuario = p_id_usuario;
END$$

-- Clientes y emprendedores para Gestión de Usuarios (el admin no se lista)
DROP PROCEDURE IF EXISTS sp_usuario_listar_admin$$
CREATE PROCEDURE sp_usuario_listar_admin()
BEGIN
    SELECT u.id_usuario, u.nombre, u.correo, u.fecha_registro, u.estado,
           r.nombre_rol, e.nombre_negocio
    FROM usuario u
    INNER JOIN rol r ON r.id_rol = u.id_rol
    LEFT JOIN emprendimiento e ON e.id_usuario = u.id_usuario
    WHERE r.nombre_rol <> 'Administrador'
    ORDER BY u.fecha_registro DESC;
END$$

-- Totales de cuentas para las pestañas de Gestión de Usuarios
DROP PROCEDURE IF EXISTS sp_usuario_contar$$
CREATE PROCEDURE sp_usuario_contar()
BEGIN
    SELECT
        SUM(CASE WHEN r.nombre_rol <> 'Administrador' THEN 1 ELSE 0 END) AS todos,
        SUM(CASE WHEN r.nombre_rol = 'Cliente' THEN 1 ELSE 0 END) AS clientes,
        SUM(CASE WHEN r.nombre_rol = 'Emprendedor' THEN 1 ELSE 0 END) AS emprendedores,
        SUM(CASE WHEN r.nombre_rol <> 'Administrador' AND u.estado <> 'activo' THEN 1 ELSE 0 END) AS suspendidos
    FROM usuario u
    INNER JOIN rol r ON r.id_rol = u.id_rol;
END$$

-- ============================================================
-- 2. EMPRENDIMIENTO
-- ============================================================

-- Registra la solicitud de un emprendimiento (queda pendiente)
DROP PROCEDURE IF EXISTS sp_emprendimiento_insertar$$
CREATE PROCEDURE sp_emprendimiento_insertar(
    IN p_id_usuario INT,
    IN p_nombre_negocio VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_ubicacion VARCHAR(150),
    IN p_logo VARCHAR(255),
    IN p_metodos_pago VARCHAR(40)
)
BEGIN
    INSERT INTO emprendimiento (id_usuario, nombre_negocio, descripcion, ubicacion, logo, metodos_pago)
    VALUES (p_id_usuario, p_nombre_negocio, p_descripcion, p_ubicacion, NULLIF(p_logo, ''), p_metodos_pago);

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Emprendimiento completo con los datos del dueño, calificación y totales
DROP PROCEDURE IF EXISTS sp_emprendimiento_obtener$$
CREATE PROCEDURE sp_emprendimiento_obtener(IN p_id_emprendimiento INT)
BEGIN
    SELECT e.id_emprendimiento, e.id_usuario, e.nombre_negocio, e.descripcion, e.ubicacion,
           e.logo, e.metodos_pago, e.observacion_admin, e.estado_verificacion, e.fecha_solicitud,
           u.nombre AS dueno, u.correo, u.telefono, u.estado AS estado_usuario,
           (SELECT COUNT(*) FROM producto p
             WHERE p.id_emprendimiento = e.id_emprendimiento AND p.estado <> 'inactivo') AS total_productos,
           (SELECT ROUND(AVG(r.calificacion), 1) FROM resena r
             INNER JOIN producto p ON p.id_producto = r.id_producto
             WHERE p.id_emprendimiento = e.id_emprendimiento) AS calificacion,
           (SELECT COUNT(*) FROM resena r
             INNER JOIN producto p ON p.id_producto = r.id_producto
             WHERE p.id_emprendimiento = e.id_emprendimiento) AS total_resenas,
           (SELECT c.nombre FROM producto p
             INNER JOIN categoria c ON c.id_categoria = p.id_categoria
             WHERE p.id_emprendimiento = e.id_emprendimiento
             GROUP BY c.id_categoria, c.nombre
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal
    FROM emprendimiento e
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE e.id_emprendimiento = p_id_emprendimiento;
END$$

-- Emprendimiento de un usuario (portal del vendedor)
DROP PROCEDURE IF EXISTS sp_emprendimiento_obtener_por_usuario$$
CREATE PROCEDURE sp_emprendimiento_obtener_por_usuario(IN p_id_usuario INT)
BEGIN
    DECLARE v_id INT DEFAULT NULL;

    SELECT id_emprendimiento INTO v_id
    FROM emprendimiento
    WHERE id_usuario = p_id_usuario;

    CALL sp_emprendimiento_obtener(v_id);
END$$

-- Actualiza la vitrina (Mi perfil del vendedor). Si p_logo viene vacío, se deja el actual.
DROP PROCEDURE IF EXISTS sp_emprendimiento_actualizar$$
CREATE PROCEDURE sp_emprendimiento_actualizar(
    IN p_id_emprendimiento INT,
    IN p_nombre_negocio VARCHAR(120),
    IN p_descripcion TEXT,
    IN p_ubicacion VARCHAR(150),
    IN p_logo VARCHAR(255),
    IN p_metodos_pago VARCHAR(40)
)
BEGIN
    UPDATE emprendimiento
    SET nombre_negocio = p_nombre_negocio,
        descripcion    = p_descripcion,
        ubicacion      = p_ubicacion,
        logo           = IF(p_logo = '', logo, p_logo),
        metodos_pago   = p_metodos_pago
    WHERE id_emprendimiento = p_id_emprendimiento;
END$$

-- Lista emprendimientos por estado (pendiente, aprobado, rechazado) para el admin
DROP PROCEDURE IF EXISTS sp_emprendimiento_listar_por_estado$$
CREATE PROCEDURE sp_emprendimiento_listar_por_estado(IN p_estado VARCHAR(10))
BEGIN
    SELECT e.id_emprendimiento, e.id_usuario, e.nombre_negocio, e.descripcion, e.ubicacion,
           e.logo, e.metodos_pago, e.observacion_admin, e.estado_verificacion, e.fecha_solicitud,
           u.nombre AS dueno, u.correo, u.telefono, u.estado AS estado_usuario,
           (SELECT COUNT(*) FROM producto p WHERE p.id_emprendimiento = e.id_emprendimiento) AS total_productos,
           (SELECT c.nombre FROM producto p
             INNER JOIN categoria c ON c.id_categoria = p.id_categoria
             WHERE p.id_emprendimiento = e.id_emprendimiento
             GROUP BY c.id_categoria, c.nombre
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal,
           (SELECT ROUND(AVG(r.calificacion), 1) FROM resena r
             INNER JOIN producto p ON p.id_producto = r.id_producto
             WHERE p.id_emprendimiento = e.id_emprendimiento) AS calificacion
    FROM emprendimiento e
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE e.estado_verificacion = p_estado
    ORDER BY e.fecha_solicitud DESC;
END$$

-- Aprueba o rechaza una solicitud y guarda la observación del admin
DROP PROCEDURE IF EXISTS sp_emprendimiento_resolver$$
CREATE PROCEDURE sp_emprendimiento_resolver(
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(10),
    IN p_observacion VARCHAR(500)
)
BEGIN
    UPDATE emprendimiento
    SET estado_verificacion = p_estado,
        observacion_admin   = NULLIF(p_observacion, '')
    WHERE id_emprendimiento = p_id_emprendimiento;
END$$

-- Emprendimientos aprobados y activos para "Historias de Artesanos"
DROP PROCEDURE IF EXISTS sp_emprendimiento_listar_historias$$
CREATE PROCEDURE sp_emprendimiento_listar_historias()
BEGIN
    SELECT e.id_emprendimiento, e.nombre_negocio, e.descripcion, e.ubicacion, e.logo,
           e.fecha_solicitud, u.nombre AS dueno,
           (SELECT COUNT(*) FROM producto p
             WHERE p.id_emprendimiento = e.id_emprendimiento AND p.estado = 'activo') AS total_productos,
           (SELECT ROUND(AVG(r.calificacion), 1) FROM resena r
             INNER JOIN producto p ON p.id_producto = r.id_producto
             WHERE p.id_emprendimiento = e.id_emprendimiento) AS calificacion,
           (SELECT i.url FROM producto p
             INNER JOIN imagen_producto i ON i.id_producto = p.id_producto
             WHERE p.id_emprendimiento = e.id_emprendimiento
             ORDER BY p.id_producto, i.orden LIMIT 1) AS imagen,
           (SELECT c.nombre FROM producto p
             INNER JOIN categoria c ON c.id_categoria = p.id_categoria
             WHERE p.id_emprendimiento = e.id_emprendimiento
             GROUP BY c.id_categoria, c.nombre
             ORDER BY COUNT(*) DESC LIMIT 1) AS categoria_principal
    FROM emprendimiento e
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE e.estado_verificacion = 'aprobado' AND u.estado = 'activo'
    ORDER BY e.fecha_solicitud;
END$$

-- ============================================================
-- 3. CATEGORÍA
-- ============================================================

-- Categorías con cantidad de productos activos y de emprendimientos (marcas)
DROP PROCEDURE IF EXISTS sp_categoria_listar$$
CREATE PROCEDURE sp_categoria_listar()
BEGIN
    SELECT c.id_categoria, c.nombre, c.icono, c.descripcion, c.artesanal,
           COUNT(p.id_producto) AS productos,
           COUNT(DISTINCT p.id_emprendimiento) AS marcas
    FROM categoria c
    LEFT JOIN producto p ON p.id_categoria = c.id_categoria AND p.estado <> 'inactivo'
    GROUP BY c.id_categoria, c.nombre, c.icono, c.descripcion, c.artesanal
    ORDER BY c.id_categoria;
END$$

-- Una categoría por id (con su cantidad de productos, para no borrar si tiene)
DROP PROCEDURE IF EXISTS sp_categoria_obtener$$
CREATE PROCEDURE sp_categoria_obtener(IN p_id_categoria INT)
BEGIN
    SELECT c.id_categoria, c.nombre, c.icono, c.descripcion, c.artesanal,
           (SELECT COUNT(*) FROM producto p WHERE p.id_categoria = c.id_categoria) AS productos
    FROM categoria c
    WHERE c.id_categoria = p_id_categoria;
END$$

-- Busca una categoría por nombre (para no repetir nombres)
DROP PROCEDURE IF EXISTS sp_categoria_obtener_por_nombre$$
CREATE PROCEDURE sp_categoria_obtener_por_nombre(IN p_nombre VARCHAR(60))
BEGIN
    SELECT id_categoria, nombre FROM categoria WHERE nombre = p_nombre;
END$$

DROP PROCEDURE IF EXISTS sp_categoria_insertar$$
CREATE PROCEDURE sp_categoria_insertar(
    IN p_nombre VARCHAR(60),
    IN p_icono VARCHAR(40),
    IN p_descripcion VARCHAR(255),
    IN p_artesanal TINYINT
)
BEGIN
    INSERT INTO categoria (nombre, icono, descripcion, artesanal)
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
    UPDATE categoria
    SET nombre = p_nombre, descripcion = p_descripcion, artesanal = p_artesanal
    WHERE id_categoria = p_id_categoria;
END$$

-- Solo se llama cuando la categoría no tiene productos (lo revisa CategoriaService)
DROP PROCEDURE IF EXISTS sp_categoria_eliminar$$
CREATE PROCEDURE sp_categoria_eliminar(IN p_id_categoria INT)
BEGIN
    DELETE FROM categoria WHERE id_categoria = p_id_categoria;
END$$

-- ============================================================
-- 4. PRODUCTO E IMÁGENES
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
           p.id_categoria, c.nombre AS categoria,
           e.id_emprendimiento, e.nombre_negocio, e.ubicacion, e.metodos_pago,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen,
           (SELECT ROUND(AVG(r.calificacion), 1) FROM resena r
             WHERE r.id_producto = p.id_producto) AS calificacion
    FROM producto p
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo'
      AND e.estado_verificacion = 'aprobado'
      AND u.estado = 'activo'
      AND (p_texto = '' OR p.nombre LIKE CONCAT('%', p_texto, '%')
                        OR e.nombre_negocio LIKE CONCAT('%', p_texto, '%')
                        OR e.ubicacion LIKE CONCAT('%', p_texto, '%')
                        OR c.nombre LIKE CONCAT('%', p_texto, '%'))
      AND (p_provincia = '' OR e.ubicacion LIKE CONCAT('%', p_provincia, '%'))
      AND (p_categorias = '' OR FIND_IN_SET(p.id_categoria, p_categorias) > 0)
      AND (p_minimo = 0 OR p.precio >= p_minimo)
      AND (p_maximo = 0 OR p.precio <= p_maximo)
      AND (p_pago = '' OR FIND_IN_SET(p_pago, e.metodos_pago) > 0)
    ORDER BY
        CASE WHEN p_orden = 'precio_asc' THEN p.precio END ASC,
        CASE WHEN p_orden = 'precio_desc' THEN p.precio END DESC,
        CASE WHEN p_orden = 'recientes' THEN p.id_producto END DESC,
        calificacion DESC,
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
    FROM producto p
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo'
      AND e.estado_verificacion = 'aprobado'
      AND u.estado = 'activo'
      AND (p_texto = '' OR p.nombre LIKE CONCAT('%', p_texto, '%')
                        OR e.nombre_negocio LIKE CONCAT('%', p_texto, '%')
                        OR e.ubicacion LIKE CONCAT('%', p_texto, '%')
                        OR c.nombre LIKE CONCAT('%', p_texto, '%'))
      AND (p_provincia = '' OR e.ubicacion LIKE CONCAT('%', p_provincia, '%'))
      AND (p_categorias = '' OR FIND_IN_SET(p.id_categoria, p_categorias) > 0)
      AND (p_minimo = 0 OR p.precio >= p_minimo)
      AND (p_maximo = 0 OR p.precio <= p_maximo)
      AND (p_pago = '' OR FIND_IN_SET(p_pago, e.metodos_pago) > 0);
END$$

-- Productos de un emprendimiento. p_solo_activos = 1 para la vitrina pública.
DROP PROCEDURE IF EXISTS sp_producto_listar_por_emprendimiento$$
CREATE PROCEDURE sp_producto_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_solo_activos TINYINT)
BEGIN
    SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.cantidad_disponible, p.estado,
           p.id_categoria, c.nombre AS categoria,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM producto p
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    WHERE p.id_emprendimiento = p_id_emprendimiento
      AND (p_solo_activos = 0 OR p.estado = 'activo')
    ORDER BY p.id_producto DESC;
END$$

-- Un producto con su emprendimiento
DROP PROCEDURE IF EXISTS sp_producto_obtener$$
CREATE PROCEDURE sp_producto_obtener(IN p_id_producto INT)
BEGIN
    SELECT p.id_producto, p.id_emprendimiento, p.id_categoria, p.nombre, p.descripcion,
           p.precio, p.cantidad_disponible, p.estado, c.nombre AS categoria,
           e.nombre_negocio, e.estado_verificacion,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM producto p
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    WHERE p.id_producto = p_id_producto;
END$$

-- Productos del carrito (ids separados por coma) con los datos del emprendimiento
DROP PROCEDURE IF EXISTS sp_producto_listar_por_ids$$
CREATE PROCEDURE sp_producto_listar_por_ids(IN p_ids VARCHAR(500))
BEGIN
    SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.cantidad_disponible, p.estado,
           c.nombre AS categoria,
           e.id_emprendimiento, e.nombre_negocio, e.ubicacion, e.logo, e.metodos_pago,
           u.nombre AS dueno, u.telefono,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM producto p
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE FIND_IN_SET(p.id_producto, p_ids) > 0
    ORDER BY e.id_emprendimiento, p.id_producto;
END$$

-- Sugerencias para el carrito: productos activos mejor calificados
DROP PROCEDURE IF EXISTS sp_producto_recomendados$$
CREATE PROCEDURE sp_producto_recomendados(IN p_limite INT, IN p_excluir VARCHAR(500))
BEGIN
    SELECT p.id_producto, p.nombre, p.precio, e.nombre_negocio, e.ubicacion,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen,
           (SELECT AVG(r.calificacion) FROM resena r WHERE r.id_producto = p.id_producto) AS calificacion
    FROM producto p
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE p.estado = 'activo' AND e.estado_verificacion = 'aprobado' AND u.estado = 'activo'
      AND (p_excluir = '' OR FIND_IN_SET(p.id_producto, p_excluir) = 0)
    ORDER BY calificacion DESC, p.id_producto
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
    INSERT INTO producto (id_emprendimiento, id_categoria, nombre, descripcion, precio, cantidad_disponible, estado)
    VALUES (p_id_emprendimiento, p_id_categoria, p_nombre, p_descripcion, p_precio, p_cantidad,
            IF(p_cantidad = 0, 'agotado', 'activo'));

    SELECT LAST_INSERT_ID() AS id;
END$$

-- Solo actualiza si el producto es de ese emprendimiento (seguridad)
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
    UPDATE producto
    SET id_categoria        = p_id_categoria,
        nombre              = p_nombre,
        descripcion         = p_descripcion,
        precio              = p_precio,
        cantidad_disponible = p_cantidad,
        estado = CASE
                     WHEN estado = 'inactivo' THEN 'inactivo'
                     WHEN p_cantidad = 0 THEN 'agotado'
                     ELSE 'activo'
                 END
    WHERE id_producto = p_id_producto AND id_emprendimiento = p_id_emprendimiento;
END$$

-- Pausa o activa un producto (interruptor de Mis Productos)
DROP PROCEDURE IF EXISTS sp_producto_cambiar_estado$$
CREATE PROCEDURE sp_producto_cambiar_estado(
    IN p_id_producto INT,
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(10)
)
BEGIN
    UPDATE producto
    SET estado = p_estado
    WHERE id_producto = p_id_producto AND id_emprendimiento = p_id_emprendimiento;
END$$

-- Guarda la foto principal del producto (reemplaza la anterior)
DROP PROCEDURE IF EXISTS sp_imagen_producto_guardar$$
CREATE PROCEDURE sp_imagen_producto_guardar(IN p_id_producto INT, IN p_url VARCHAR(255))
BEGIN
    DELETE FROM imagen_producto WHERE id_producto = p_id_producto AND orden = 1;
    INSERT INTO imagen_producto (id_producto, url, orden) VALUES (p_id_producto, p_url, 1);
END$$

-- ============================================================
-- 5. RESEÑAS Y FAVORITOS
-- ============================================================

-- Reseñas de todos los productos de un emprendimiento (vitrina)
DROP PROCEDURE IF EXISTS sp_resena_listar_por_emprendimiento$$
CREATE PROCEDURE sp_resena_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_limite INT)
BEGIN
    SELECT r.id_resena, r.calificacion, r.comentario, r.fecha,
           u.nombre AS autor, u.provincia, p.nombre AS producto
    FROM resena r
    INNER JOIN producto p ON p.id_producto = r.id_producto
    INNER JOIN usuario u ON u.id_usuario = r.id_usuario
    WHERE p.id_emprendimiento = p_id_emprendimiento
    ORDER BY r.fecha DESC
    LIMIT p_limite;
END$$

DROP PROCEDURE IF EXISTS sp_favorito_listar$$
CREATE PROCEDURE sp_favorito_listar(IN p_id_usuario INT)
BEGIN
    SELECT p.id_producto, p.nombre, p.precio, e.id_emprendimiento, e.nombre_negocio, e.ubicacion,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM favorito f
    INNER JOIN producto p ON p.id_producto = f.id_producto
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    WHERE f.id_usuario = p_id_usuario
    ORDER BY f.fecha_agregado DESC;
END$$

-- Agrega un favorito (si ya existía no hace nada)
DROP PROCEDURE IF EXISTS sp_favorito_agregar$$
CREATE PROCEDURE sp_favorito_agregar(IN p_id_usuario INT, IN p_id_producto INT)
BEGIN
    INSERT IGNORE INTO favorito (id_usuario, id_producto) VALUES (p_id_usuario, p_id_producto);
END$$

DROP PROCEDURE IF EXISTS sp_favorito_eliminar$$
CREATE PROCEDURE sp_favorito_eliminar(IN p_id_usuario INT, IN p_id_producto INT)
BEGIN
    DELETE FROM favorito WHERE id_usuario = p_id_usuario AND id_producto = p_id_producto;
END$$

-- ============================================================
-- 6. PEDIDOS
-- ============================================================

-- Encabezado del pedido. Devuelve el id nuevo.
DROP PROCEDURE IF EXISTS sp_pedido_insertar$$
CREATE PROCEDURE sp_pedido_insertar(IN p_id_usuario INT, IN p_metodo_pago VARCHAR(10), IN p_total DECIMAL(10, 2))
BEGIN
    INSERT INTO pedido (id_usuario, metodo_pago, total) VALUES (p_id_usuario, p_metodo_pago, p_total);
    SELECT LAST_INSERT_ID() AS id;
END$$

-- Una línea del pedido. Rebaja el inventario y marca agotado si llega a 0.
DROP PROCEDURE IF EXISTS sp_detalle_pedido_insertar$$
CREATE PROCEDURE sp_detalle_pedido_insertar(
    IN p_id_pedido INT,
    IN p_id_producto INT,
    IN p_cantidad INT,
    IN p_precio DECIMAL(10, 2)
)
BEGIN
    INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
    VALUES (p_id_pedido, p_id_producto, p_cantidad, p_precio, p_cantidad * p_precio);

    UPDATE producto
    SET cantidad_disponible = GREATEST(cantidad_disponible - p_cantidad, 0),
        estado = IF(cantidad_disponible = 0 AND estado = 'activo', 'agotado', estado)
    WHERE id_producto = p_id_producto;
END$$

DROP PROCEDURE IF EXISTS sp_direccion_entrega_insertar$$
CREATE PROCEDURE sp_direccion_entrega_insertar(
    IN p_id_pedido INT,
    IN p_nombre VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_provincia VARCHAR(20),
    IN p_canton VARCHAR(60),
    IN p_distrito VARCHAR(60),
    IN p_direccion VARCHAR(255)
)
BEGIN
    INSERT INTO direccion_entrega (id_pedido, nombre_receptor, telefono, provincia, canton, distrito, direccion_exacta)
    VALUES (p_id_pedido, p_nombre, p_telefono, p_provincia, p_canton, p_distrito, p_direccion);
END$$

-- Encabezado y dirección de un pedido. p_id_usuario = 0 no filtra por dueño (admin).
DROP PROCEDURE IF EXISTS sp_pedido_obtener$$
CREATE PROCEDURE sp_pedido_obtener(IN p_id_pedido INT, IN p_id_usuario INT)
BEGIN
    SELECT pe.id_pedido, pe.id_usuario, pe.fecha_pedido, pe.metodo_pago, pe.total,
           d.nombre_receptor, d.telefono, d.provincia, d.canton, d.distrito, d.direccion_exacta,
           u.nombre AS cliente, u.correo
    FROM pedido pe
    INNER JOIN usuario u ON u.id_usuario = pe.id_usuario
    LEFT JOIN direccion_entrega d ON d.id_pedido = pe.id_pedido
    WHERE pe.id_pedido = p_id_pedido
      AND (p_id_usuario = 0 OR pe.id_usuario = p_id_usuario);
END$$

-- Líneas de un pedido con el emprendimiento de cada producto (para agrupar por SINPE)
DROP PROCEDURE IF EXISTS sp_detalle_pedido_listar$$
CREATE PROCEDURE sp_detalle_pedido_listar(IN p_id_pedido INT)
BEGIN
    SELECT dp.id_detalle, dp.id_producto, dp.cantidad, dp.precio_unitario, dp.subtotal, dp.estado_entrega,
           p.nombre, e.id_emprendimiento, e.nombre_negocio, u.nombre AS dueno, u.telefono,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = p.id_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM detalle_pedido dp
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    INNER JOIN usuario u ON u.id_usuario = e.id_usuario
    WHERE dp.id_pedido = p_id_pedido
    ORDER BY e.id_emprendimiento, dp.id_detalle;
END$$

-- Pedidos de un cliente (Mi perfil > Mis pedidos), una fila por pedido
DROP PROCEDURE IF EXISTS sp_pedido_listar_por_cliente$$
CREATE PROCEDURE sp_pedido_listar_por_cliente(IN p_id_usuario INT)
BEGIN
    -- La consulta de adentro agrupa por pedido; la de afuera agrega la foto del primer producto
    SELECT t.*,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = t.primer_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM (
    SELECT pe.id_pedido, pe.fecha_pedido, pe.metodo_pago, pe.total,
           COUNT(dp.id_detalle) AS lineas,
           SUM(dp.cantidad) AS unidades,
           GROUP_CONCAT(DISTINCT e.nombre_negocio ORDER BY e.nombre_negocio SEPARATOR ', ') AS emprendimientos,
           SUM(CASE WHEN dp.estado_entrega = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
           SUM(CASE WHEN dp.estado_entrega = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
           SUM(CASE WHEN dp.estado_entrega = 'entregado' THEN 1 ELSE 0 END) AS entregados,
           SUM(CASE WHEN dp.estado_entrega = 'enviado' THEN 1 ELSE 0 END) AS enviados,
           MIN(dp.id_producto) AS primer_producto
    FROM pedido pe
    INNER JOIN detalle_pedido dp ON dp.id_pedido = pe.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    INNER JOIN emprendimiento e ON e.id_emprendimiento = p.id_emprendimiento
    WHERE pe.id_usuario = p_id_usuario
    GROUP BY pe.id_pedido, pe.fecha_pedido, pe.metodo_pago, pe.total
    ) t
    ORDER BY t.fecha_pedido DESC;
END$$

-- Pedidos que recibió un emprendimiento: solo sus productos, una fila por pedido
DROP PROCEDURE IF EXISTS sp_pedido_listar_por_emprendimiento$$
CREATE PROCEDURE sp_pedido_listar_por_emprendimiento(IN p_id_emprendimiento INT, IN p_limite INT)
BEGIN
    SELECT t.*,
           (SELECT i.url FROM imagen_producto i
             WHERE i.id_producto = t.primer_producto ORDER BY i.orden LIMIT 1) AS imagen
    FROM (
    SELECT pe.id_pedido, pe.fecha_pedido, pe.metodo_pago,
           u.nombre AS cliente, u.telefono AS telefono_cliente,
           d.canton, d.provincia, d.direccion_exacta,
           SUM(dp.subtotal) AS total,
           SUM(dp.cantidad) AS unidades,
           COUNT(dp.id_detalle) AS lineas,
           GROUP_CONCAT(CONCAT(dp.cantidad, '× ', p.nombre) ORDER BY dp.id_detalle SEPARATOR ' · ') AS productos,
           MIN(dp.estado_entrega) AS estado_minimo,
           SUM(CASE WHEN dp.estado_entrega = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
           SUM(CASE WHEN dp.estado_entrega IN ('confirmado', 'enviado') THEN 1 ELSE 0 END) AS confirmados,
           SUM(CASE WHEN dp.estado_entrega = 'entregado' THEN 1 ELSE 0 END) AS entregados,
           SUM(CASE WHEN dp.estado_entrega = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
           MIN(dp.id_producto) AS primer_producto
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    INNER JOIN usuario u ON u.id_usuario = pe.id_usuario
    LEFT JOIN direccion_entrega d ON d.id_pedido = pe.id_pedido
    WHERE p.id_emprendimiento = p_id_emprendimiento
    GROUP BY pe.id_pedido, pe.fecha_pedido, pe.metodo_pago, u.nombre, u.telefono,
             d.canton, d.provincia, d.direccion_exacta
    ) t
    ORDER BY t.fecha_pedido DESC
    LIMIT p_limite;
END$$

-- Cambia el estado de entrega de las líneas de un emprendimiento dentro de un pedido
DROP PROCEDURE IF EXISTS sp_detalle_pedido_cambiar_estado$$
CREATE PROCEDURE sp_detalle_pedido_cambiar_estado(
    IN p_id_pedido INT,
    IN p_id_emprendimiento INT,
    IN p_estado VARCHAR(12)
)
BEGIN
    UPDATE detalle_pedido dp
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    SET dp.estado_entrega = p_estado
    WHERE dp.id_pedido = p_id_pedido
      AND p.id_emprendimiento = p_id_emprendimiento
      AND dp.estado_entrega NOT IN ('entregado', 'rechazado');

    SELECT ROW_COUNT() AS filas;
END$$

-- ============================================================
-- 7. REPORTES
--    No cuentan las líneas rechazadas. Fechas en formato 'AAAA-MM-DD'.
-- ============================================================

-- Ventas por día de un emprendimiento (PHP las agrupa por semana o mes)
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_ventas_diarias$$
CREATE PROCEDURE sp_reporte_vendedor_ventas_diarias(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT DATE(pe.fecha_pedido) AS fecha, SUM(dp.subtotal) AS monto
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    WHERE p.id_emprendimiento = p_id_emprendimiento
      AND dp.estado_entrega <> 'rechazado'
      AND DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta
    GROUP BY DATE(pe.fecha_pedido)
    ORDER BY fecha;
END$$

-- Totales del período: ventas, pedidos y % entregado
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_resumen$$
CREATE PROCEDURE sp_reporte_vendedor_resumen(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT COALESCE(SUM(dp.subtotal), 0) AS ventas,
           COUNT(DISTINCT dp.id_pedido) AS pedidos,
           COUNT(dp.id_detalle) AS lineas,
           SUM(CASE WHEN dp.estado_entrega = 'entregado' THEN 1 ELSE 0 END) AS entregadas,
           SUM(CASE WHEN dp.estado_entrega = 'pendiente' THEN 1 ELSE 0 END) AS pendientes
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    WHERE p.id_emprendimiento = p_id_emprendimiento
      AND dp.estado_entrega <> 'rechazado'
      AND DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta;
END$$

-- Producto más vendido del período
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_producto_top$$
CREATE PROCEDURE sp_reporte_vendedor_producto_top(IN p_id_emprendimiento INT, IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT p.nombre, SUM(dp.cantidad) AS unidades
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    WHERE p.id_emprendimiento = p_id_emprendimiento
      AND dp.estado_entrega <> 'rechazado'
      AND DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta
    GROUP BY p.id_producto, p.nombre
    ORDER BY unidades DESC
    LIMIT 1;
END$$

-- Números del inicio del portal del vendedor
DROP PROCEDURE IF EXISTS sp_reporte_vendedor_inventario$$
CREATE PROCEDURE sp_reporte_vendedor_inventario(IN p_id_emprendimiento INT)
BEGIN
    SELECT SUM(CASE WHEN estado <> 'inactivo' THEN 1 ELSE 0 END) AS catalogo,
           SUM(CASE WHEN estado <> 'inactivo' AND cantidad_disponible <= 5 THEN 1 ELSE 0 END) AS stock_bajo
    FROM producto
    WHERE id_emprendimiento = p_id_emprendimiento;
END$$

-- Totales generales de la plataforma para un período (admin)
DROP PROCEDURE IF EXISTS sp_reporte_admin_resumen$$
CREATE PROCEDURE sp_reporte_admin_resumen(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT COALESCE(SUM(dp.subtotal), 0) AS ventas,
           COUNT(DISTINCT dp.id_pedido) AS pedidos,
           COUNT(DISTINCT CASE WHEN pe.metodo_pago = 'sinpe' THEN pe.id_pedido END) AS pedidos_sinpe
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    WHERE dp.estado_entrega <> 'rechazado'
      AND DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta;
END$$

-- Ventas por categoría en el período (barras del reporte del admin)
DROP PROCEDURE IF EXISTS sp_reporte_admin_ventas_categoria$$
CREATE PROCEDURE sp_reporte_admin_ventas_categoria(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT c.nombre AS categoria, SUM(dp.subtotal) AS monto
    FROM detalle_pedido dp
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    INNER JOIN categoria c ON c.id_categoria = p.id_categoria
    WHERE dp.estado_entrega <> 'rechazado'
      AND DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta
    GROUP BY c.id_categoria, c.nombre
    ORDER BY monto DESC
    LIMIT 6;
END$$

-- Pedidos por provincia de entrega en el período (cobertura territorial)
DROP PROCEDURE IF EXISTS sp_reporte_admin_ventas_provincia$$
CREATE PROCEDURE sp_reporte_admin_ventas_provincia(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT d.provincia, COUNT(*) AS pedidos
    FROM pedido pe
    INNER JOIN direccion_entrega d ON d.id_pedido = pe.id_pedido
    WHERE DATE(pe.fecha_pedido) BETWEEN p_desde AND p_hasta
    GROUP BY d.provincia
    ORDER BY pedidos DESC;
END$$

-- Números del inicio del panel del administrador
DROP PROCEDURE IF EXISTS sp_reporte_admin_inicio$$
CREATE PROCEDURE sp_reporte_admin_inicio()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM emprendimiento WHERE estado_verificacion = 'pendiente') AS pendientes,
        (SELECT COUNT(*) FROM emprendimiento e INNER JOIN usuario u ON u.id_usuario = e.id_usuario
          WHERE e.estado_verificacion = 'aprobado' AND u.estado = 'activo') AS activos,
        (SELECT COUNT(*) FROM emprendimiento
          WHERE estado_verificacion = 'aprobado'
            AND YEAR(fecha_solicitud) = YEAR(CURDATE()) AND MONTH(fecha_solicitud) = MONTH(CURDATE())) AS nuevos_mes,
        (SELECT COUNT(*) FROM usuario u INNER JOIN rol r ON r.id_rol = u.id_rol
          WHERE r.nombre_rol <> 'Administrador') AS usuarios,
        (SELECT COUNT(*) FROM categoria) AS categorias,
        (SELECT COUNT(*) FROM categoria WHERE artesanal = 1) AS categorias_artesanales;
END$$

DELIMITER ;
