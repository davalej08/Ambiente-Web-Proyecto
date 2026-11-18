/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* Validación de formularios en el cliente */

/* ==========================================================
   VENTICA · validaciones.js
   Revisa los formularios de Iniciar sesión, Crear cuenta, Registro de
   emprendedor, Recuperar y Cambiar contraseña
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

// Cédula: de 9 a 12 números (se permiten guiones y espacios: 1-0234-0567)
function cedulaValida(cedula) {
    let numeros = "";
    for (let i = 0; i < cedula.length; i++) {
        const letra = cedula.charAt(i);
        if (letra >= "0" && letra <= "9") {
            numeros = numeros + letra;
        } else if (letra !== "-" && letra !== " ") {
            return false;
        }
    }
    return numeros.length >= 9 && numeros.length <= 12;
}

// Cédula, nombre y apellidos (registro/datos_persona.php). Devuelve true si hay errores.
function revisarDatosPersona() {
    const revisiones = [
        ["cedula", cedulaValida(document.getElementById("cedula").value.trim())],
        ["nombre", document.getElementById("nombre").value.trim().length >= 2],
        ["primer_apellido", document.getElementById("primer_apellido").value.trim().length >= 2]
    ];
    let hayErrores = false;

    for (let i = 0; i < revisiones.length; i++) {
        marcarError(revisiones[i][0], !revisiones[i][1]);
        if (!revisiones[i][1]) {
            hayErrores = true;
        }
    }
    return hayErrores;
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
        let hayErrores = revisarDatosPersona();

        const correo = document.getElementById("correo").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const contrasena = document.getElementById("contrasena").value;
        const aceptaTerminos = document.getElementById("terminos").checked;

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
}

// ---------- Teléfono: pone el guion mientras se escribe (88880000 -> 8888-0000) ----------
// Sirve para Crear cuenta y para el Registro de emprendedor
const campoTelefono = document.getElementById("telefono");

if (campoTelefono) {
    campoTelefono.addEventListener("input", function () {
        const numeros = campoTelefono.value.replace("-", "");

        if (numeros.length > 4) {
            campoTelefono.value = numeros.substring(0, 4) + "-" + numeros.substring(4, 8);
        } else {
            campoTelefono.value = numeros;
        }
    });
}

// ---------- Formulario: Registro de emprendedor ----------
const formRegistroVendedor = document.getElementById("formRegistroVendedor");

if (formRegistroVendedor) {
    // Contador de caracteres de la descripción
    const campoDescripcion = document.getElementById("descripcion");
    const contador = document.getElementById("contadorDescripcion");

    contador.textContent = campoDescripcion.value.length;
    campoDescripcion.addEventListener("input", function () {
        contador.textContent = campoDescripcion.value.length;
    });

    formRegistroVendedor.addEventListener("submit", function (evento) {
        let hayErrores = revisarDatosPersona();

        const correo = document.getElementById("correo").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const contrasena = document.getElementById("contrasena").value;
        const negocio = document.getElementById("negocio").value.trim();
        const provincia = document.getElementById("provincia").value;
        const canton = document.getElementById("canton").value;
        const distrito = document.getElementById("distrito").value;
        const descripcion = campoDescripcion.value.trim();
        const logo = document.getElementById("logo").files[0];
        const aceptaTerminos = document.getElementById("terminos").checked;

        // Cada condición: si es válida se despinta, si no se pinta en rojo
        const revisiones = [
            ["correo", correoValido(correo)],
            ["telefono", telefonoValido(telefono)],
            ["contrasena", contrasena.length >= 8],
            ["negocio", negocio.length >= 3],
            ["provincia", provincia !== ""],
            ["canton", canton !== ""],
            ["distrito", distrito !== ""],
            ["descripcion", descripcion.length >= 20],
            ["terminos", aceptaTerminos]
        ];

        for (let i = 0; i < revisiones.length; i++) {
            const esValido = revisiones[i][1];
            marcarError(revisiones[i][0], !esValido);
            if (!esValido) {
                hayErrores = true;
            }
        }

        // El logo es opcional; si viene, debe ser JPG o PNG de máximo 2 MB
        if (logo) {
            const tipoValido = logo.type === "image/png" || logo.type === "image/jpeg";
            const pesoValido = logo.size <= 2 * 1024 * 1024;

            if (tipoValido && pesoValido) {
                marcarError("logo", false);
            } else {
                marcarError("logo", true);
                hayErrores = true;
            }
        } else {
            marcarError("logo", false);
        }

        if (hayErrores) {
            evento.preventDefault();
        }
    });
}

// ---------- Formulario: Recuperar contraseña ----------
const formRecuperar = document.getElementById("formRecuperar");

if (formRecuperar) {
    formRecuperar.addEventListener("submit", function (evento) {
        const correo = document.getElementById("correo").value.trim();

        if (correoValido(correo)) {
            marcarError("correo", false);
        } else {
            marcarError("correo", true);
            evento.preventDefault();
        }
    });
}

// ---------- Formulario: Cambiar contraseña ----------
const formCambiar = document.getElementById("formCambiar");

// Revisa cada requisito y lo pinta en verde si se cumple. Devuelve true si cumple todos.
function revisarRequisitos(contrasena) {
    const tieneLargo = contrasena.length >= 8;
    let tieneNumero = false;
    let tieneMayuscula = false;

    // Se revisa letra por letra
    for (let i = 0; i < contrasena.length; i++) {
        const letra = contrasena[i];

        if (letra >= "0" && letra <= "9") {
            tieneNumero = true;
        } else if (letra !== letra.toLowerCase()) {
            tieneMayuscula = true;
        }
    }

    document.getElementById("reqLargo").classList.toggle("es-cumplido", tieneLargo);
    document.getElementById("reqNumero").classList.toggle("es-cumplido", tieneNumero);
    document.getElementById("reqMayuscula").classList.toggle("es-cumplido", tieneMayuscula);

    return tieneLargo && tieneNumero && tieneMayuscula;
}

if (formCambiar) {
    const campoContrasena = document.getElementById("contrasena");

    campoContrasena.addEventListener("input", function () {
        revisarRequisitos(campoContrasena.value);
    });

    formCambiar.addEventListener("submit", function (evento) {
        let hayErrores = false;
        const contrasena = campoContrasena.value;
        const confirmacion = document.getElementById("confirmacion").value;

        if (revisarRequisitos(contrasena)) {
            marcarError("contrasena", false);
        } else {
            marcarError("contrasena", true);
            hayErrores = true;
        }

        if (confirmacion !== "" && confirmacion === contrasena) {
            marcarError("confirmacion", false);
        } else {
            marcarError("confirmacion", true);
            hayErrores = true;
        }

        if (hayErrores) {
            evento.preventDefault();
        }
    });
}

// ---------- Contacto ----------
const formContacto = document.getElementById("formContacto");

if (formContacto) {
    formContacto.addEventListener("submit", function (evento) {
        let hayErrores = false;

        // Cada revisión: [id del campo, ¿está bien?]
        const revisiones = [
            ["nombre", document.getElementById("nombre").value.trim().length >= 3],
            ["correo", correoValido(document.getElementById("correo").value.trim())],
            ["motivo", document.getElementById("motivo").value !== ""],
            ["mensaje", document.getElementById("mensaje").value.trim().length >= 15]
        ];

        for (let i = 0; i < revisiones.length; i++) {
            marcarError(revisiones[i][0], !revisiones[i][1]);
            if (!revisiones[i][1]) {
                hayErrores = true;
            }
        }

        if (hayErrores) {
            evento.preventDefault();
        }
    });
}
