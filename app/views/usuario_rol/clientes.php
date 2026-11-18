<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 
 * Admin · Gestión de Usuarios 
 * Ruta: admin/usuarios
 
 * Lista de cuentas de clientes y emprendedores. El administrador busca, filtra por pestañas, ordena, invita usuarios, edita datos 
 * y suspende o activa cuentas. (no se eliminan)
 */

$titulo       = 'Gestión de Usuarios · Administración';
$estilos      = ['admin', 'admin_gestion'];
$scripts      = ['validaciones', 'usuarios_admin'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'usuarios';

if (!isset($usuarios)) {
    // rol: "cliente" o "emprendedor". contacto: persona a cargo del emprendimiento.
    $usuarios = [
        ['nombre' => 'Valeria Jiménez Solano',  'contacto' => '',               'rol' => 'cliente',     'correo' => 'valeria.jimenez@gmail.com',     'registro' => '2026-10-14', 'estado' => 'activo'],
        ['nombre' => 'Taller Baula Artesanías', 'contacto' => 'Carlos Alvarado', 'rol' => 'emprendedor', 'correo' => 'hola@tallerbaula.cr',           'registro' => '2026-10-02', 'estado' => 'activo'],
        ['nombre' => 'Café Don Mincho',         'contacto' => 'Minor Chacón',    'rol' => 'emprendedor', 'correo' => 'ventas@cafedonmincho.cr',       'registro' => '2026-09-21', 'estado' => 'activo'],
        ['nombre' => 'Esteban Mora Castro',     'contacto' => '',               'rol' => 'cliente',     'correo' => 'esteban.mora@hotmail.com',      'registro' => '2026-09-08', 'estado' => 'suspendido'],
        ['nombre' => 'Sofía Alfaro Cordero',    'contacto' => '',               'rol' => 'cliente',     'correo' => 'sofi.alfaro@outlook.com',       'registro' => '2026-08-30', 'estado' => 'activo'],
        ['nombre' => 'Cerámica Boruca Taller',  'contacto' => 'Ana Lucía Rojas', 'rol' => 'emprendedor', 'correo' => 'contacto@ceramicaboruca.cr',    'registro' => '2026-08-17', 'estado' => 'activo'],
    ];
}
if (!isset($totales)) {
    $totales = ['todos' => 4850, 'clientes' => 4530, 'emprendedores' => 320, 'suspendidos' => 3];
}

$totalUsuarios = count($usuarios);
$meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dic'];

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_admin.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_admin.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- Encabezado -->
            <section class="ad-cabecera">
                <div>
                    <p class="ad-etiquetas"><span class="ad-sobretitulo">Comunidad Ventica Costa Rica</span></p>
                    <h1 class="ad-titulo">Gestión de Usuarios</h1>
                    <p class="ad-descripcion">Control y moderación de cuentas de clientes y emprendedores costarricenses.</p>
                </div>
                <div class="ad-cabecera__acciones">
                    <span class="ad-pastilla ad-pastilla--verde"><span class="punto"></span> <?php echo number_format($totales['todos'], 0, '.', ','); ?> cuentas registradas</span>
                    <button class="btn btn--oscuro" type="button" id="botonInvitar"><?php echo icono('mas'); ?> Invitar Usuario</button>
                </div>
            </section>

            <!-- Mensaje de confirmación o de error (lo muestra usuarios_admin.js) -->
            <div id="mensajeUsuario" class="mensaje mensaje--exito ad-oculto" role="status">
                <p id="textoMensajeUsuario"></p>
            </div>

            <!-- Formulario invitar / editar usuario -->
            <section class="tarjeta ad-formulario ad-oculto" id="panelFormulario">
                <h2 class="titulo-seccion" id="tituloFormulario">Invitar usuario</h2>

                <form class="formulario" id="formUsuario" method="post" action="<?php echo e(ruta('admin/usuarios/guardar')); ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                    <!-- -1 = usuario nuevo; si es un número, es la posición del usuario que se edita -->
                    <input type="hidden" id="indiceEdicion" name="indice" value="-1">
                    <!-- Id del usuario en la base de datos (0 = usuario nuevo) -->
                    <input type="hidden" id="idUsuarioEdicion" name="id_usuario" value="0">

                    <div class="ad-formulario__campos">
                        <!-- Solo al invitar: la cédula no se cambia después -->
                        <div id="campo-cedula" class="campo">
                            <label class="campo__etiqueta" for="cedula">Cédula de la persona</label>
                            <input class="campo__entrada" id="cedula" name="cedula" type="text" inputmode="numeric" maxlength="14" placeholder="Ej. 102340567">
                            <p class="campo__error">Escribí la cédula solo con números (9 a 12).</p>
                        </div>
                        <div id="campo-nombre" class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombre completo o comercial</label>
                            <input class="campo__entrada" id="nombre" name="nombre" type="text" maxlength="60" placeholder="Ej. Laura Vargas Mora">
                            <p class="campo__error">Escribí un nombre de al menos 3 letras.</p>
                        </div>
                        <div id="campo-correo" class="campo">
                            <label class="campo__etiqueta" for="correo">Correo electrónico</label>
                            <input class="campo__entrada" id="correo" name="correo" type="email" maxlength="80" placeholder="nombre@correo.com">
                            <p class="campo__error" id="errorCorreo">Escribí un correo válido.</p>
                        </div>
                        <div class="campo">
                            <label class="campo__etiqueta" for="rol">Rol</label>
                            <select class="campo__entrada" id="rol" name="rol">
                                <option value="cliente">Cliente</option>
                                <option value="emprendedor">Emprendedor</option>
                            </select>
                        </div>
                        <div id="campo-contacto" class="campo">
                            <label class="campo__etiqueta" for="contacto">Persona a cargo <small>(solo emprendedores)</small></label>
                            <input class="campo__entrada" id="contacto" name="contacto" type="text" maxlength="60" placeholder="Ej. Carlos Alvarado">
                            <p class="campo__error">Escribí quién atiende el emprendimiento.</p>
                        </div>
                        <div id="campo-contrasena" class="campo">
                            <label class="campo__etiqueta" for="contrasenaTemporal">Contraseña temporal</label>
                            <div class="ad-fila-campo">
                                <input class="campo__entrada" id="contrasenaTemporal" name="contrasena" type="text" readonly placeholder="Tocá Generar">
                                <button class="btn btn--borde btn--sm" type="button" id="generarContrasena">Generar</button>
                            </div>
                            <p class="campo__nota" id="notaContrasena">El usuario la cambia al iniciar sesión.</p>
                            <p class="campo__error">Generá una contraseña temporal.</p>
                        </div>
                    </div>

                    <div class="ad-formulario__botones">
                        <button class="btn btn--texto" type="button" id="cancelarUsuario">Cancelar</button>
                        <button class="btn btn--oscuro" type="button" id="guardarUsuario"><span id="textoGuardar">Enviar invitación</span></button>
                    </div>
                </form>
            </section>

            <!-- Buscador y orden -->
            <div class="ad-herramientas solo-pantalla">
                <div class="ad-buscador">
                    <?php echo icono('buscar'); ?>
                    <label class="oculto-visual" for="buscarUsuario">Buscar usuario</label>
                    <input id="buscarUsuario" type="search" placeholder="Buscar por nombre, correo o emprendimiento...">
                </div>
                <div class="ad-orden">
                    <label for="ordenUsuarios">Ordenar:</label>
                    <select id="ordenUsuarios">
                        <option value="recientes">Más recientes</option>
                        <option value="antiguos">Más antiguos</option>
                    </select>
                </div>
            </div>

            <!-- Pestañas por tipo de cuenta -->
            <div class="ad-pestanas solo-pantalla" role="tablist">
                <button class="ad-pestana es-activo" type="button" id="pestanaTodos">Todos <span><?php echo number_format($totales['todos'], 0, '.', ','); ?></span></button>
                <button class="ad-pestana" type="button" id="pestanaClientes">Clientes <span><?php echo number_format($totales['clientes'], 0, '.', ','); ?></span></button>
                <button class="ad-pestana" type="button" id="pestanaEmprendedores">Emprendedores <span><?php echo $totales['emprendedores']; ?></span></button>
                <button class="ad-pestana" type="button" id="pestanaSuspendidos">Suspendidos <span id="contadorSuspendidos"><?php echo $totales['suspendidos']; ?></span></button>
            </div>

            <!-- Lista de usuarios (la arma PHP con un for) -->
            <input type="hidden" id="totalUsuarios" value="<?php echo $totalUsuarios; ?>">

            <ul class="ad-lista ad-lista--usuarios" id="listaUsuarios">
                <?php for ($i = 0; $i < $totalUsuarios; $i++): ?>
                    <?php
                    $usuario = $usuarios[$i];

                    // Iniciales: primera letra de las dos primeras palabras (función de Vista.php)
                    $iniciales = iniciales($usuario['nombre']);

                    // Fecha de registro: 2026-10-14 → 14 oct 2026
                    $partes = explode('-', $usuario['registro']);
                    $fecha  = (int) $partes[2] . ' ' . $meses[(int) $partes[1] - 1] . ' ' . $partes[0];

                    // Ícono del botón de detalle según el tipo de cuenta
                    if ($usuario['estado'] === 'suspendido') {
                        $iconoDetalle = 'info';
                    } elseif ($usuario['rol'] === 'emprendedor') {
                        $iconoDetalle = 'tienda';
                    } else {
                        $iconoDetalle = 'ojo';
                    }

                    $suspendido = $usuario['estado'] === 'suspendido';
                    ?>
                    <li class="ad-usuario<?php echo $suspendido ? ' ad-usuario--suspendido' : ''; ?>" id="usuario-<?php echo $i; ?>">
                        <span class="ad-avatar ad-avatar--<?php echo $usuario['rol']; ?>" id="avatar-<?php echo $i; ?>">
                            <?php echo e($iniciales); ?>
                            <span class="ad-avatar__punto<?php echo $suspendido ? ' es-suspendido' : ''; ?>" id="punto-<?php echo $i; ?>"></span>
                        </span>

                        <div class="ad-usuario__info">
                            <h2>
                                <span id="nombre-<?php echo $i; ?>"><?php echo e($usuario['nombre']); ?></span>
                                <small id="contactoTexto-<?php echo $i; ?>"><?php echo $usuario['contacto'] !== '' ? '(' . e($usuario['contacto']) . ')' : ''; ?></small>
                                <span class="ad-chip <?php echo $usuario['rol'] === 'emprendedor' ? 'ad-chip--teal' : 'ad-chip--suave'; ?>" id="rolTexto-<?php echo $i; ?>"><?php echo $usuario['rol'] === 'emprendedor' ? 'Emprendedor' : 'Cliente'; ?></span>
                            </h2>
                            <p id="correoTexto-<?php echo $i; ?>"><?php echo e($usuario['correo']); ?></p>
                        </div>

                        <div class="ad-usuario__fecha">
                            <small>Registrado</small>
                            <span><?php echo e($fecha); ?></span>
                        </div>

                        <p class="ad-usuario__estado<?php echo $suspendido ? ' es-suspendido' : ''; ?>" id="estadoTexto-<?php echo $i; ?>">
                            <span class="punto"></span> <span id="estadoNombre-<?php echo $i; ?>"><?php echo $suspendido ? 'Suspendido' : 'Activo'; ?></span>
                        </p>

                        <div class="ad-usuario__acciones solo-pantalla">
                            <button class="ad-boton-icono" type="button" id="detalle-<?php echo $i; ?>" aria-label="Ver y editar datos de <?php echo e($usuario['nombre']); ?>"><?php echo icono($iconoDetalle); ?></button>
                            <?php if ($suspendido): ?>
                                <button class="ad-boton ad-boton--exito" type="button" id="cambiarEstado-<?php echo $i; ?>">Activar</button>
                            <?php else: ?>
                                <button class="ad-boton ad-boton--peligro" type="button" id="cambiarEstado-<?php echo $i; ?>">Suspender</button>
                            <?php endif; ?>
                        </div>

                        <!-- Datos que usan el buscador y el formulario -->
                        <input type="hidden" id="rol-<?php echo $i; ?>" value="<?php echo e($usuario['rol']); ?>">
                        <input type="hidden" id="contacto-<?php echo $i; ?>" value="<?php echo e($usuario['contacto']); ?>">
                        <input type="hidden" id="correo-<?php echo $i; ?>" value="<?php echo e($usuario['correo']); ?>">
                        <input type="hidden" id="estado-<?php echo $i; ?>" value="<?php echo e($usuario['estado']); ?>">
                        <input type="hidden" id="id-<?php echo $i; ?>" value="<?php echo (int) valor($usuario, 'id', 0); ?>">
                    </li>
                <?php endfor; ?>
            </ul>

            <p class="ad-vacio ad-oculto" id="sinResultados">No hay usuarios que coincidan con la búsqueda.</p>

            <!-- Formulario escondido para suspender o activar: usuarios_admin.js le pone el id -->
            <form id="formEstadoUsuario" method="post" action="<?php echo e(ruta('admin/usuarios/estado')); ?>" hidden>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="id_usuario" id="idUsuarioEstado" value="">
            </form>

            <!-- Pie: cantidad y paginación -->
            <div class="ad-pie">
                <p>Mostrando 1-<?php echo $totalUsuarios; ?> de <?php echo number_format($totales['todos'], 0, '.', ','); ?> usuarios registrados</p>
                <nav class="ad-paginacion solo-pantalla" aria-label="Paginación">
                    <a href="#" aria-label="Página anterior">‹</a>
                    <a href="#" class="es-actual" aria-current="page">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <span>…</span>
                    <a href="#">809</a>
                    <a href="#" aria-label="Página siguiente">›</a>
                </nav>
            </div>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
