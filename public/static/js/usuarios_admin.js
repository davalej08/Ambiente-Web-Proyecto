/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · usuarios_admin.js
   Admin | Gestión de Usuarios 
   1. Variables y funciones de apoyo
   2. Buscar, pestañas y ordenar
   3. Suspender / activar cuenta
   4. Formulario: invitar usuario y editar datos
   Usa marcarError() y correoValido() de validaciones.js (se carga antes).
   ========================================================== */

// ---------- 1. Variables y funciones de apoyo ----------

// Mientras no exista AdminController, los cambios solo se hacen en la pantalla.
const MODO_DEMO = true;

// Cada fila tiene id "usuario-0", "usuario-1", ... (las genera PHP)
let totalUsuarios = Number(document.getElementById("totalUsuarios").value);

// Pestaña elegida: "todos", "cliente", "emprendedor" o "suspendido"
let pestanaActual = "todos";

function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById("mensajeUsuario");
    document.getElementById("textoMensajeUsuario").textContent = texto;

    if (tipo === "error") {
        mensaje.className = "mensaje mensaje--error";
    } else {
        mensaje.className = "mensaje mensaje--exito";
    }
    mensaje.style.display = "flex";
}

// Revisa si otro usuario ya usa ese correo (sin importar mayúsculas)
function correoRepetido(correo, indiceIgnorar) {
    for (let i = 0; i < totalUsuarios; i++) {
        if (i !== indiceIgnorar) {
            const correoFila = document.getElementById("correo-" + i).value;

            if (correoFila.toLowerCase() === correo.toLowerCase()) {
                return true;
            }
        }
    }
    return false;
}

// ---------- 2. Buscar, pestañas y ordenar ----------
function filtrarUsuarios() {
    const texto = document.getElementById("buscarUsuario").value.trim().toLowerCase();
    let visibles = 0;

    for (let i = 0; i < totalUsuarios; i++) {
        const rol = document.getElementById("rol-" + i).value;
        const estado = document.getElementById("estado-" + i).value;
        const datos = (document.getElementById("nombre-" + i).textContent + " " +
                       document.getElementById("contacto-" + i).value + " " +
                       document.getElementById("correo-" + i).value).toLowerCase();
        let mostrar = true;

        // Pestaña
        switch (pestanaActual) {
            case "cliente":
                mostrar = rol === "cliente";
                break;
            case "emprendedor":
                mostrar = rol === "emprendedor";
                break;
            case "suspendido":
                mostrar = estado === "suspendido";
                break;
            default:
                mostrar = true;
        }

        // Texto del buscador
        if (texto !== "" && !datos.includes(texto)) {
            mostrar = false;
        }

        if (mostrar) {
            document.getElementById("usuario-" + i).style.display = "flex";
            visibles++;
        } else {
            document.getElementById("usuario-" + i).style.display = "none";
        }
    }

    if (visibles === 0) {
        document.getElementById("sinResultados").style.display = "block";
    } else {
        document.getElementById("sinResultados").style.display = "none";
    }
}

document.getElementById("buscarUsuario").addEventListener("keyup", filtrarUsuarios);

// Marca la pestaña tocada y vuelve a filtrar
function elegirPestana(pestana, idBoton) {
    pestanaActual = pestana;
    document.getElementById("pestanaTodos").className = "ad-pestana";
    document.getElementById("pestanaClientes").className = "ad-pestana";
    document.getElementById("pestanaEmprendedores").className = "ad-pestana";
    document.getElementById("pestanaSuspendidos").className = "ad-pestana";
    document.getElementById(idBoton).className = "ad-pestana es-activo";
    filtrarUsuarios();
}

document.getElementById("pestanaTodos").addEventListener("click", function () {
    elegirPestana("todos", "pestanaTodos");
});
document.getElementById("pestanaClientes").addEventListener("click", function () {
    elegirPestana("cliente", "pestanaClientes");
});
document.getElementById("pestanaEmprendedores").addEventListener("click", function () {
    elegirPestana("emprendedor", "pestanaEmprendedores");
});
document.getElementById("pestanaSuspendidos").addEventListener("click", function () {
    elegirPestana("suspendido", "pestanaSuspendidos");
});

// Ordenar: PHP manda la lista de la más reciente a la más antigua.
// appendChild mueve un elemento que ya existe al final de la lista.
document.getElementById("ordenUsuarios").addEventListener("change", function () {
    const lista = document.getElementById("listaUsuarios");

    if (this.value === "antiguos") {
        for (let i = totalUsuarios - 1; i >= 0; i--) {
            lista.appendChild(document.getElementById("usuario-" + i));
        }
    } else {
        for (let i = 0; i < totalUsuarios; i++) {
            lista.appendChild(document.getElementById("usuario-" + i));
        }
    }
});

// ---------- 3. Suspender / activar ----------
function cambiarEstado(indice) {
    const nombre = document.getElementById("nombre-" + indice).textContent;
    const estado = document.getElementById("estado-" + indice);
    const boton = document.getElementById("cambiarEstado-" + indice);
    const contador = document.getElementById("contadorSuspendidos");

    if (estado.value === "activo") {
        estado.value = "suspendido";
        boton.textContent = "Activar";
        boton.className = "ad-boton ad-boton--exito";
        document.getElementById("estadoNombre-" + indice).textContent = "Suspendido";
        document.getElementById("estadoTexto-" + indice).className = "ad-usuario__estado es-suspendido";
        document.getElementById("punto-" + indice).className = "ad-avatar__punto es-suspendido";
        document.getElementById("usuario-" + indice).className = "ad-usuario ad-usuario--suspendido";
        contador.textContent = Number(contador.textContent) + 1;
        mostrarMensaje("La cuenta de " + nombre + " fue suspendida. No podrá iniciar sesión, pero su historial se conserva.", "exito");
    } else {
        estado.value = "activo";
        boton.textContent = "Suspender";
        boton.className = "ad-boton ad-boton--peligro";
        document.getElementById("estadoNombre-" + indice).textContent = "Activo";
        document.getElementById("estadoTexto-" + indice).className = "ad-usuario__estado";
        document.getElementById("punto-" + indice).className = "ad-avatar__punto";
        document.getElementById("usuario-" + indice).className = "ad-usuario";
        contador.textContent = Number(contador.textContent) - 1;
        mostrarMensaje("La cuenta de " + nombre + " fue activada de nuevo.", "exito");
    }

    filtrarUsuarios();
}

// ---------- 4. Formulario ----------
function limpiarErrores() {
    marcarError("nombre", false);
    marcarError("correo", false);
    marcarError("contacto", false);
    marcarError("contrasena", false);
}

// El campo "Persona a cargo" solo se muestra para emprendedores
function mostrarCampoContacto() {
    if (document.getElementById("rol").value === "emprendedor") {
        document.getElementById("campo-contacto").style.display = "flex";
    } else {
        document.getElementById("campo-contacto").style.display = "none";
    }
}

function abrirFormulario() {
    limpiarErrores();
    mostrarCampoContacto();
    document.getElementById("panelFormulario").style.display = "block";
    document.getElementById("nombre").focus();
}

function cerrarFormulario() {
    document.getElementById("panelFormulario").style.display = "none";
}

document.getElementById("rol").addEventListener("change", mostrarCampoContacto);
document.getElementById("cancelarUsuario").addEventListener("click", cerrarFormulario);

// Botón "Invitar Usuario": formulario vacío
document.getElementById("botonInvitar").addEventListener("click", function () {
    document.getElementById("formUsuario").reset();
    document.getElementById("indiceEdicion").value = "-1";
    document.getElementById("tituloFormulario").textContent = "Invitar usuario";
    document.getElementById("textoGuardar").textContent = "Enviar invitación";
    document.getElementById("notaContrasena").textContent = "El usuario la cambia al iniciar sesión.";
    abrirFormulario();
});

// Botón de detalle de cada fila: formulario con los datos de ese usuario
function abrirDetalle(indice) {
    document.getElementById("formUsuario").reset();
    document.getElementById("indiceEdicion").value = indice;
    document.getElementById("nombre").value = document.getElementById("nombre-" + indice).textContent;
    document.getElementById("correo").value = document.getElementById("correo-" + indice).value;
    document.getElementById("rol").value = document.getElementById("rol-" + indice).value;
    document.getElementById("contacto").value = document.getElementById("contacto-" + indice).value;
    document.getElementById("tituloFormulario").textContent = "Datos de " + document.getElementById("nombre-" + indice).textContent;
    document.getElementById("textoGuardar").textContent = "Guardar cambios";
    document.getElementById("notaContrasena").textContent = "Opcional: generá una nueva solo si el usuario la olvidó.";
    abrirFormulario();
}

for (let i = 0; i < totalUsuarios; i++) {
    document.getElementById("detalle-" + i).addEventListener("click", function () {
        abrirDetalle(i);
    });
    document.getElementById("cambiarEstado-" + i).addEventListener("click", function () {
        if (MODO_DEMO) {
            cambiarEstado(i);
        }
    });
}

// Contraseña temporal: 10 caracteres al azar (for + Math.random + charAt)
document.getElementById("generarContrasena").addEventListener("click", function () {
    const caracteres = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
    let contrasena = "";

    for (let i = 0; i < 10; i++) {
        const posicion = Math.floor(Math.random() * caracteres.length);
        contrasena = contrasena + caracteres.charAt(posicion);
    }

    document.getElementById("contrasenaTemporal").value = contrasena;
    marcarError("contrasena", false);
});

document.getElementById("guardarUsuario").addEventListener("click", function () {
    const indice = Number(document.getElementById("indiceEdicion").value);
    const esNuevo = indice === -1;
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const rol = document.getElementById("rol").value;
    const contacto = document.getElementById("contacto").value.trim();
    const contrasena = document.getElementById("contrasenaTemporal").value;
    let hayErrores = false;

    if (nombre.length < 3) {
        marcarError("nombre", true);
        hayErrores = true;
    } else {
        marcarError("nombre", false);
    }

    // Correo: formato y que no esté repetido
    if (!correoValido(correo)) {
        document.getElementById("errorCorreo").textContent = "Escribí un correo válido.";
        marcarError("correo", true);
        hayErrores = true;
    } else if (correoRepetido(correo, indice)) {
        document.getElementById("errorCorreo").textContent = "Ya existe una cuenta con ese correo.";
        marcarError("correo", true);
        hayErrores = true;
    } else {
        marcarError("correo", false);
    }

    if (rol === "emprendedor" && contacto.length < 3) {
        marcarError("contacto", true);
        hayErrores = true;
    } else {
        marcarError("contacto", false);
    }

    // Al invitar, la contraseña temporal es obligatoria
    if (esNuevo && contrasena === "") {
        marcarError("contrasena", true);
        hayErrores = true;
    } else {
        marcarError("contrasena", false);
    }

    if (hayErrores) {
        return;
    }

    if (!MODO_DEMO) {
        document.getElementById("formUsuario").submit();
        return;
    }

    if (esNuevo) {
        // En la versión final el servidor crea la cuenta y envía el correo
        mostrarMensaje("Se envió la invitación a " + correo + ". La cuenta aparecerá en la lista cuando la confirme.", "exito");
    } else {
        document.getElementById("nombre-" + indice).textContent = nombre;
        document.getElementById("correo-" + indice).value = correo;
        document.getElementById("correoTexto-" + indice).textContent = correo;
        document.getElementById("rol-" + indice).value = rol;

        const chip = document.getElementById("rolTexto-" + indice);
        if (rol === "emprendedor") {
            document.getElementById("contacto-" + indice).value = contacto;
            document.getElementById("contactoTexto-" + indice).textContent = "(" + contacto + ")";
            chip.textContent = "Emprendedor";
            chip.className = "ad-chip ad-chip--teal";
        } else {
            document.getElementById("contacto-" + indice).value = "";
            document.getElementById("contactoTexto-" + indice).textContent = "";
            chip.textContent = "Cliente";
            chip.className = "ad-chip ad-chip--suave";
        }

        mostrarMensaje("Se guardaron los datos de " + nombre + ".", "exito");
    }

    cerrarFormulario();
    filtrarUsuarios();
});
