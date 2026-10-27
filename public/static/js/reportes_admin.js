/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · reportes_admin.js
   Admin | Reportes Generales
   1. Ancho de las barras de categorías
   2. Cambio de período
   3. Exportar resumen (imprimir)
   ========================================================== */

// ---------- 1. Barras ----------
// Cada barra mide lo mismo que su porcentaje del total
const totalBarras = Number(document.getElementById("totalBarras").value);

for (let i = 0; i < totalBarras; i++) {
    const porcentaje = Number(document.getElementById("porcentaje-" + i).value);
    document.getElementById("barra-" + i).style.width = porcentaje + "%";
}

// ---------- 2. Período ----------
// Al elegir otro período se envía el formulario (GET) y PHP recalcula los datos
document.getElementById("periodo").addEventListener("change", function () {
    document.getElementById("formPeriodo").submit();
});

// ---------- 3. Exportar ----------
// La impresión del navegador permite "Guardar como PDF"
document.getElementById("botonExportar").addEventListener("click", function () {
    window.print();
});
