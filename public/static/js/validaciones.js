/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* Validación de formularios en el cliente */

/* ==========================================================
   VENTICA · validaciones.js
   Revisa los formularios de Iniciar sesión y Crear cuenta
   ANTES de enviarlos. Si algo está mal, pinta el campo en rojo
   y no deja enviar. (El servidor en PHP igual vuelve a validar.)
   ========================================================== */

// Pinta o despinta un campo en rojo.
// idCampo es el nombre del campo: "correo" busca el <div id="campo-correo">
function marcarError(idCampo, hayError) {
    const contenedor = document.getElementById("campo-" + idCampo);

    if (hayError) {
        contenedor.classList.add("es-invalido");
    } else {
        contenedor.classList.remove("es-invalido");
    }
}

// Un correo válido tiene algo antes de la @ y un punto después de la @
function correoValido(correo) {
    const posicionArroba = correo.indexOf("@");
    const posicionPunto = correo.lastIndexOf(".");

    return posicionArroba > 0 && posicionPunto > posicionArroba + 1 && posicionPunto < correo.length - 2;
}

// Teléfono de Costa Rica: 8 números (con o sin guion)
function telefonoValido(telefono) {
    const numeros = telefono.replace("-", "");

    return numeros.length === 8 && !isNaN(Number(numeros));
}

// ---------- Formulario: Iniciar sesión ----------
const formLogin = document.getElementById("formLogin");

if (formLogin) {
    formLogin.addEventListener("submit", function (evento) {
        let hayErrores = false;

        const correo = document.getElementById("correo").value.trim();
        const contrasena = document.getElementById("contrasena").value;

        if (correoValido(correo)) {
            marcarError("correo", false);
        } else {
            marcarError("correo", true);
            hayErrores = true;
        }

        if (contrasena.length > 0) {
            marcarError("contrasena", false);
        } else {
            marcarError("contrasena", true);
            hayErrores = true;
        }

        // preventDefault() detiene el envío del formulario
        if (hayErrores) {
            evento.preventDefault();
        }
    });
}

// ---------- Formulario: Crear cuenta ----------
const formRegistro = document.getElementById("formRegistro");

if (formRegistro) {
    formRegistro.addEventListener("submit", function (evento) {
        let hayErrores = false;

        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const contrasena = document.getElementById("contrasena").value;
        const aceptaTerminos = document.getElementById("terminos").checked;

        if (nombre.length >= 3) {
            marcarError("nombre", false);
        } else {
            marcarError("nombre", true);
            hayErrores = true;
        }

        if (correoValido(correo)) {
            marcarError("correo", false);
        } else {
            marcarError("correo", true);
            hayErrores = true;
        }

        if (telefonoValido(telefono)) {
            marcarError("telefono", false);
        } else {
            marcarError("telefono", true);
            hayErrores = true;
        }

        if (contrasena.length >= 8) {
            marcarError("contrasena", false);
        } else {
            marcarError("contrasena", true);
            hayErrores = true;
        }

        if (aceptaTerminos) {
            marcarError("terminos", false);
        } else {
            marcarError("terminos", true);
            hayErrores = true;
        }

        if (hayErrores) {
            evento.preventDefault();
        }
    });

    // Pone el guion al teléfono mientras se escribe: 88880000 -> 8888-0000
    const campoTelefono = document.getElementById("telefono");

    campoTelefono.addEventListener("input", function () {
        const numeros = campoTelefono.value.replace("-", "");

        if (numeros.length > 4) {
            campoTelefono.value = numeros.substring(0, 4) + "-" + numeros.substring(4, 8);
        } else {
            campoTelefono.value = numeros;
        }
    });
}
