/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ==========================================================
   VENTICA · historias.js
   Historias de Artesanos: filtrar las tarjetas por provincia
   sin recargar la página.
   ========================================================== */

const botonesProvincia = document.getElementsByClassName("filtro-historia");
const tarjetasHistoria = document.getElementsByClassName("historia-tarjeta");

// Muestra solo las tarjetas de la provincia escogida ("todas" = todas)
function filtrarHistorias(provincia) {
    let visibles = 0;

    for (let i = 0; i < tarjetasHistoria.length; i++) {
        const provinciaTarjeta = tarjetasHistoria[i].getAttribute("data-provincia");

        if (provincia === "todas" || provinciaTarjeta === provincia) {
            tarjetasHistoria[i].hidden = false;
            visibles++;
        } else {
            tarjetasHistoria[i].hidden = true;
        }
    }

    document.getElementById("sinHistorias").hidden = visibles > 0 || tarjetasHistoria.length === 0;
}

for (let i = 0; i < botonesProvincia.length; i++) {
    botonesProvincia[i].addEventListener("click", function () {
        // Se apaga el botón que estaba activo y se prende el tocado
        for (let j = 0; j < botonesProvincia.length; j++) {
            botonesProvincia[j].classList.remove("es-activo");
            botonesProvincia[j].setAttribute("aria-pressed", "false");
        }
        botonesProvincia[i].classList.add("es-activo");
        botonesProvincia[i].setAttribute("aria-pressed", "true");

        filtrarHistorias(botonesProvincia[i].getAttribute("data-provincia"));
    });
}
