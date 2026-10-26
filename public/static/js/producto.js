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

/* ==========================================================
   Vitrina del emprendimiento (producto/detalle.php)
   5. Filtrar el catálogo por categoría con los chips.
   6. Compartir la vitrina: copia el enlace de la página.
   ========================================================== */

// 5. Filtros del catálogo de la vitrina
const chipsFiltro = document.getElementsByClassName("chip-filtro");
const productosVitrina = document.getElementsByClassName("producto-vitrina");

for (let i = 0; i < chipsFiltro.length; i++) {
    chipsFiltro[i].addEventListener("click", function () {
        const filtro = chipsFiltro[i].getAttribute("data-filtro");

        // Se marca solo el chip escogido
        for (let j = 0; j < chipsFiltro.length; j++) {
            chipsFiltro[j].classList.remove("es-activo");
        }
        chipsFiltro[i].classList.add("es-activo");

        // Se muestran solo los productos de esa categoría
        let visibles = 0;
        for (let j = 0; j < productosVitrina.length; j++) {
            const categoria = productosVitrina[j].getAttribute("data-categoria");

            if (filtro === "todos" || categoria === filtro) {
                productosVitrina[j].hidden = false;
                visibles++;
            } else {
                productosVitrina[j].hidden = true;
            }
        }

        // Mensaje si la categoría no tiene productos
        document.getElementById("sinCoincidencias").hidden = visibles > 0;
    });
}

// 6. Compartir la vitrina
const botonCompartir = document.getElementById("compartirVitrina");

if (botonCompartir) {
    botonCompartir.addEventListener("click", function () {
        navigator.clipboard.writeText(window.location.href);
        alert("Enlace de la vitrina copiado. ¡Ya lo podés compartir!");
    });
}
