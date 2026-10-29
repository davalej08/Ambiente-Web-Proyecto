/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ==========================================================
   VENTICA · confirmacion.js
   Botón "Copiar número" del SINPE de cada emprendimiento.
   ========================================================== */

const botonesCopiar = document.getElementsByClassName("copiar-sinpe");

for (let i = 0; i < botonesCopiar.length; i++) {
    const boton = botonesCopiar[i];

    boton.addEventListener("click", function () {
        const numero = boton.getAttribute("data-numero").replace("-", "");
        const texto = boton.getElementsByTagName("span")[0];

        // Copia el número al portapapeles y avisa en el mismo botón
        navigator.clipboard.writeText(numero);
        texto.textContent = "¡Copiado!";
        boton.classList.add("es-copiado");
    });
}
