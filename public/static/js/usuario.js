/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ==========================================================
   VENTICA · usuario.js
   Pantalla Mi perfil del cliente:
   1. Pestañas: Mis datos / Mis pedidos / Favoritos.
   2. Validar el formulario de datos personales.
   3. Quitar un producto de favoritos.
   Usa marcarError, correoValido y telefonoValido de validaciones.js
   ========================================================== */

// ---------- 1. Pestañas ----------
const pestanas = document.getElementsByClassName("pestana");

// Muestra el panel de la pestaña escogida y oculta los demás
function mostrarPestana(idPestana) {
    for (let i = 0; i < pestanas.length; i++) {
        const pestana = pestanas[i];
        const panel = document.getElementById(pestana.getAttribute("data-panel"));
        const esLaElegida = pestana.id === idPestana;

        pestana.classList.toggle("es-activa", esLaElegida);
        pestana.setAttribute("aria-selected", esLaElegida ? "true" : "false");
        panel.classList.toggle("es-visible", esLaElegida);
    }
}

for (let i = 0; i < pestanas.length; i++) {
    pestanas[i].addEventListener("click", function () {
        mostrarPestana(pestanas[i].id);
    });
}

// Si la dirección termina en #pedidos o #favoritos, se abre esa pestaña de una vez
if (window.location.hash === "#pedidos") {
    mostrarPestana("tab-pedidos");
} else if (window.location.hash === "#favoritos") {
    mostrarPestana("tab-favoritos");
}

// ---------- 2. Formulario de datos personales ----------
const formPerfil = document.getElementById("formPerfil");

if (formPerfil) {
    formPerfil.addEventListener("submit", function (evento) {
        let hayErrores = false;
        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const mensaje = document.getElementById("mensajePerfil");

        const primerApellido = document.getElementById("primer_apellido").value.trim();

        if (nombre.length >= 2) {
            marcarError("nombre", false);
        } else {
            marcarError("nombre", true);
            hayErrores = true;
        }

        if (primerApellido.length >= 2) {
            marcarError("primer_apellido", false);
        } else {
            marcarError("primer_apellido", true);
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

        if (hayErrores) {
            evento.preventDefault();
            mensaje.textContent = "Revisá los campos marcados en rojo.";
            mensaje.className = "perfil__mensaje es-error";
        }
    });
}

// ---------- 3. Quitar de favoritos ----------
const botonesQuitar = document.getElementsByClassName("quitar-favorito");

for (let i = 0; i < botonesQuitar.length; i++) {
    const boton = botonesQuitar[i];

    boton.addEventListener("click", function () {
        // botón -> div.producto__media -> article -> li (la tarjeta completa)
        const tarjeta = boton.parentElement.parentElement.parentElement;
        tarjeta.remove();

        // Se guarda en el servidor (ClienteController::quitarFavorito)
        document.getElementById("idFavoritoQuitar").value = boton.getAttribute("data-id");
        document.getElementById("formQuitarFavorito").submit();

        const cantidad = document.getElementById("listaFavoritos").children.length;
        document.getElementById("cantidadFavoritos").textContent = cantidad;

        if (cantidad === 0) {
            document.getElementById("favoritosVacio").hidden = false;
        }
    });
}
