/* ==========================================================
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · general.js
   Se carga al final del <body> en TODAS las páginas (desde footer.php).
   1. Abrir y cerrar el menú en celular.
   2. Cerrar el mensaje de éxito / error.
   3. Mostrar u ocultar la contraseña (botón con el ojo).
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

// 2. Cerrar el mensaje
const botonCerrarMensaje = document.getElementById("cerrarMensaje");

if (botonCerrarMensaje) {
    botonCerrarMensaje.addEventListener("click", function () {
        document.getElementById("mensajeGeneral").remove();
    });
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