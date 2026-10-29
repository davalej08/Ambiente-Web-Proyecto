<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Rutas de la aplicación: URL -> controlador y método
 *
 *   GET  = abrir una pantalla
 *   POST = enviar un formulario (siempre lleva csrf_token)
 *
 * Para agregar una pantalla nueva: una línea aquí + el método en el controlador.
 */

// ---------- Sitio público (CatalogoController) ----------
$router->get('', 'CatalogoController', 'inicio');
$router->get('catalogo', 'CatalogoController', 'catalogo');
$router->get('producto/detalle', 'CatalogoController', 'detalle');
$router->get('historias', 'CatalogoController', 'historias');
$router->get('terminos', 'CatalogoController', 'terminos');
$router->get('ayuda', 'CatalogoController', 'ayuda');
$router->get('contacto', 'CatalogoController', 'contacto');
$router->post('contacto', 'CatalogoController', 'enviarContacto');
$router->get('plantilla', 'CatalogoController', 'plantilla');   // guía de componentes para el equipo

// ---------- Acceso (AuthController) RF-01, RF-02, RF-04, RF-07 ----------
$router->get('auth/login', 'AuthController', 'login');
$router->post('auth/login', 'AuthController', 'procesarLogin');
$router->get('auth/registro', 'AuthController', 'registro');
$router->post('auth/registro', 'AuthController', 'procesarRegistro');
$router->get('auth/registro-vendedor', 'AuthController', 'registroVendedor');
$router->post('auth/registro-vendedor', 'AuthController', 'procesarRegistroVendedor');
$router->get('auth/recuperar', 'AuthController', 'recuperar');
$router->post('auth/recuperar', 'AuthController', 'procesarRecuperar');
$router->get('auth/cambiar-contrasena', 'AuthController', 'cambiarContrasena');
$router->post('auth/cambiar-contrasena', 'AuthController', 'procesarCambiarContrasena');
$router->get('auth/logout', 'AuthController', 'logout');

// ---------- Carrito y pago (CarritoController, PagoController) RF-14, RF-15 ----------
$router->get('carrito', 'CarritoController', 'ver');
$router->post('carrito/agregar', 'CarritoController', 'agregar');
$router->post('carrito/quitar', 'CarritoController', 'quitar');
$router->post('carrito/checkout', 'PagoController', 'checkout');
$router->get('carrito/confirmacion', 'PagoController', 'confirmacion');

// ---------- Cliente (ClienteController) ----------
$router->get('usuario/perfil', 'ClienteController', 'perfil');
$router->post('usuario/perfil/guardar', 'ClienteController', 'guardarPerfil');
$router->post('favoritos/alternar', 'ClienteController', 'alternarFavorito');
$router->post('usuario/favoritos/quitar', 'ClienteController', 'quitarFavorito');

// ---------- Portal del emprendedor (VendedorController, ReporteController) RF-12, RF-13 ----------
$router->get('vendedor/dashboard', 'VendedorController', 'dashboard');
$router->get('vendedor/productos', 'VendedorController', 'productos');
$router->get('vendedor/productos/nuevo', 'VendedorController', 'nuevoProducto');
$router->get('vendedor/productos/editar', 'VendedorController', 'editarProducto');
$router->post('vendedor/productos/guardar', 'VendedorController', 'guardarProducto');
$router->post('vendedor/productos/actualizar', 'VendedorController', 'guardarProducto');
$router->post('vendedor/productos/disponibilidad', 'VendedorController', 'disponibilidad');
$router->get('vendedor/pedidos', 'VendedorController', 'pedidos');
$router->post('vendedor/pedidos/confirmar', 'VendedorController', 'confirmarPedido');
$router->post('vendedor/pedidos/entregar', 'VendedorController', 'entregarPedido');
$router->get('vendedor/pedidos/imprimir', 'VendedorController', 'imprimirPedido');
$router->get('vendedor/perfil', 'VendedorController', 'perfil');
$router->post('vendedor/perfil/guardar', 'VendedorController', 'guardarPerfil');
$router->get('vendedor/reportes', 'ReporteController', 'vendedor');
$router->get('vendedor/reportes/pdf', 'ReporteController', 'vendedorImprimir');

// ---------- Administración (AdminController, CategoriaController, ReporteController) RF-03, RF-05, RF-10 ----------
$router->get('admin/dashboard', 'AdminController', 'dashboard');
$router->get('admin/solicitudes', 'AdminController', 'solicitudes');
$router->get('admin/solicitudes/detalle', 'AdminController', 'detalleSolicitud');
$router->post('admin/solicitudes/resolver', 'AdminController', 'resolverSolicitud');
$router->get('admin/emprendimientos', 'AdminController', 'emprendimientos');
$router->post('admin/emprendimientos/estado', 'AdminController', 'estadoEmprendimiento');
$router->get('admin/usuarios', 'AdminController', 'usuarios');
$router->post('admin/usuarios/guardar', 'AdminController', 'guardarUsuario');
$router->post('admin/usuarios/estado', 'AdminController', 'estadoUsuario');
$router->get('admin/buscar', 'AdminController', 'buscar');
$router->get('admin/categorias', 'CategoriaController', 'listado');
$router->post('admin/categorias/guardar', 'CategoriaController', 'guardar');
$router->post('admin/categorias/eliminar', 'CategoriaController', 'eliminar');
$router->get('admin/reportes', 'ReporteController', 'admin');
