/* ==========================================================
   VENTICA · producto.js
   Interacciones del catálogo:
   1. Mostrar / ocultar los filtros en celular.
   2. Ordenar: al cambiar el select se envía el formulario.
   3. Resaltar el método de pago escogido.
   4. Favoritos: el corazón se llena o se vacía al tocarlo.
      (Por ahora es visual; cuando exista el controlador de
      favoritos se guarda en la base de datos.)
   ========================================================== */

// 1. Filtros en celular
const botonFiltros = document.getElementById("abrirFiltros");

if (botonFiltros) {
    botonFiltros.addEventListener("click", function () {
        const formFiltros = document.getElementById("formFiltros");
        formFiltros.classList.toggle("es-abierto");

        if (formFiltros.classList.contains("es-abierto")) {
            document.getElementById("textoFiltros").textContent = "Ocultar filtros";
            botonFiltros.setAttribute("aria-expanded", "true");
        } else {
            document.getElementById("textoFiltros").textContent = "Mostrar filtros";
            botonFiltros.setAttribute("aria-expanded", "false");
        }
    });
}

// 2. Ordenar por (evento change, como en los ejemplos de clase)
const selectOrden = document.getElementById("orden");

if (selectOrden) {
    selectOrden.addEventListener("change", function () {
        document.getElementById("formOrden").submit();
    });
}

// 3. Método de pago: se resalta la opción marcada
const opcionesPago = document.getElementsByName("pago");

for (let i = 0; i < opcionesPago.length; i++) {
    opcionesPago[i].addEventListener("change", function () {
        // Primero se quita el resaltado de todas...
        for (let j = 0; j < opcionesPago.length; j++) {
            opcionesPago[j].parentElement.classList.remove("es-marcada");
        }
        // ...y se resalta solo la escogida
        opcionesPago[i].parentElement.classList.add("es-marcada");
    });
}

// 4. Favoritos
const corazones = document.getElementsByClassName("producto__favorito");

for (let i = 0; i < corazones.length; i++) {
    corazones[i].addEventListener("click", function () {
        const boton = corazones[i];

        if (boton.getAttribute("aria-pressed") === "true") {
            boton.setAttribute("aria-pressed", "false");
            boton.setAttribute("aria-label", "Agregar a favoritos");
        } else {
            boton.setAttribute("aria-pressed", "true");
            boton.setAttribute("aria-label", "Quitar de favoritos");
        }
    });
}
