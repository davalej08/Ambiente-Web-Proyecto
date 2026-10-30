/* ==========================================================
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · general.js
   Se carga al final del <body> en TODAS las páginas (desde footer.php).
   1. Abrir y cerrar el menú en celular.
   2. Toast: cerrar el mensaje de éxito / error.
   3. Mostrar u ocultar la contraseña (botón con el ojo).
   4. Pantalla de carga y botones "cargando".
   ========================================================== */

// 1. Menú en celular
const botonMenu = document.getElementById("navbarToggle");

if (botonMenu) {
    botonMenu.addEventListener("click", function () {
        const menu = document.getElementById("navbarMenu");
        menu.classList.toggle("es-abierto");

        if (menu.classList.contains("es-abierto")) {
            botonMenu.setAttribute("aria-expanded", "true");
            botonMenu.setAttribute("aria-label", "Cerrar menú");
        } else {
            botonMenu.setAttribute("aria-expanded", "false");
            botonMenu.setAttribute("aria-label", "Abrir menú");
        }
    });
}

// 2. Toast (mensaje flotante): se cierra con la X o solo a los 6 segundos.
//    Los de error se quedan hasta que la persona los cierre.
const toast = document.getElementById("mensajeGeneral");

function cerrarToast() {
    if (toast === null || toast.classList.contains("es-saliendo")) {
        return;
    }
    // Primero la animación de salida y después se quita de la página
    toast.classList.add("es-saliendo");
    setTimeout(function () {
        toast.remove();
    }, 300);
}

if (toast) {
    document.getElementById("cerrarMensaje").addEventListener("click", cerrarToast);

    if (toast.getAttribute("data-cierre") === "auto") {
        let tiempoRestante = 6000;
        let inicio = Date.now();
        let temporizador = setTimeout(cerrarToast, tiempoRestante);

        // Mientras el mouse está encima, el tiempo se detiene
        toast.addEventListener("mouseenter", function () {
            clearTimeout(temporizador);
            tiempoRestante = tiempoRestante - (Date.now() - inicio);
        });
        toast.addEventListener("mouseleave", function () {
            inicio = Date.now();
            temporizador = setTimeout(cerrarToast, tiempoRestante);
        });
    }
}

// 3. Mostrar / ocultar contraseña
// En el HTML: <input id="contrasena"> y <button id="verContrasena">
const botonVer = document.getElementById("verContrasena");

if (botonVer) {
    botonVer.addEventListener("click", function () {
        const campo = document.getElementById("contrasena");

        if (campo.type === "password") {
            campo.type = "text";
            botonVer.classList.add("es-visible");
            botonVer.setAttribute("aria-label", "Ocultar contraseña");
        } else {
            campo.type = "password";
            botonVer.classList.remove("es-visible");
            botonVer.setAttribute("aria-label", "Mostrar contraseña");
        }
    });
}

// 4. Pantalla de carga
//    Con la base de datos en la nube, cada página tarda un poquito: mientras tanto
//    se muestra el colibrí. Solo aparece si la espera pasa de 0.3 segundos.
const pantallaCarga = document.getElementById("pantallaCarga");
let esperaCarga = null;

function mostrarCarga() {
    if (pantallaCarga === null) {
        return;
    }
    clearTimeout(esperaCarga);
    esperaCarga = setTimeout(function () {
        pantallaCarga.classList.add("es-visible");
        pantallaCarga.setAttribute("aria-hidden", "false");
    }, 300);

    // Por si la página no cambia (ej. una descarga), se esconde sola a los 15 segundos
    setTimeout(ocultarCarga, 15000);
}

function ocultarCarga() {
    clearTimeout(esperaCarga);
    if (pantallaCarga) {
        pantallaCarga.classList.remove("es-visible");
        pantallaCarga.setAttribute("aria-hidden", "true");
    }
}

// beforeunload: el navegador está por salir de esta página (enlace o formulario)
window.addEventListener("beforeunload", mostrarCarga);

// pageshow: al volver con el botón "Atrás" la página puede salir de la memoria
// del navegador con la pantalla de carga puesta; aquí se quita
window.addEventListener("pageshow", ocultarCarga);

// Botón del formulario con ruedita mientras se envía.
// setTimeout(0) espera a que las validaciones de cada página terminen: si alguna
// detuvo el envío (preventDefault), el botón no cambia.
const formularios = document.getElementsByTagName("form");

for (let i = 0; i < formularios.length; i++) {
    formularios[i].addEventListener("submit", function (evento) {
        const formulario = formularios[i];

        setTimeout(function () {
            if (evento.defaultPrevented) {
                return;
            }

            const botones = formulario.getElementsByTagName("button");
            for (let j = 0; j < botones.length; j++) {
                // Solo los botones con diseño .btn (no el corazón de favoritos ni los íconos)
                if (botones[j].type === "submit" && botones[j].classList.contains("btn") && !botones[j].classList.contains("es-cargando")) {
                    const giro = document.createElement("span");
                    giro.className = "btn__giro";
                    giro.setAttribute("aria-hidden", "true");
                    botones[j].appendChild(giro);
                    botones[j].classList.add("es-cargando");
                    botones[j].setAttribute("aria-busy", "true");
                }
            }
        }, 0);
    });
}

// Al volver con "Atrás", los botones vuelven a la normalidad
window.addEventListener("pageshow", function () {
    const botonesCargando = document.getElementsByClassName("es-cargando");
    // Se recorre al revés porque la lista se achica al quitar la clase
    for (let i = botonesCargando.length - 1; i >= 0; i--) {
        const giro = botonesCargando[i].getElementsByClassName("btn__giro")[0];
        if (giro) {
            giro.remove();
        }
        botonesCargando[i].removeAttribute("aria-busy");
        botonesCargando[i].classList.remove("es-cargando");
    }
});
