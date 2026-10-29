/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ==========================================================
   VENTICA · imprimir.js
   Comprobante de pedido y reporte en PDF: abre la ventana de
   impresión del navegador (ahí se escoge "Guardar como PDF").
   ========================================================== */

const botonImprimir = document.getElementById("botonImprimir");

if (botonImprimir) {
    botonImprimir.addEventListener("click", function () {
        window.print();
    });
}

// Si la página se abrió para imprimir directamente, se abre la ventana sola
if (document.body.classList.contains("abrir-impresion")) {
    window.print();
}
