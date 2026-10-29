/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · emprendimientos_admin.js
   Pantalla Admin | Emprendimientos Activos (RF-03)
   1. Variables y mensaje
   2. Buscar y filtrar por provincia
   3. Suspender / reactivar (no se elimina, se conserva el historial)
   4. Exportar reporte (imprimir)
   ========================================================== */

// ---------- 1. Variables y mensaje ----------

// MODO_DEMO en true: los cambios solo se ven en la pantalla (sin guardar).
// En false: se envía el formulario a AdminController y se guarda en la base de datos.
const MODO_DEMO = false;

// Cada fila tiene id "emprendimiento-0", "emprendimiento-1"
const totalEmprendimientos = Number(document.getElementById("totalEmprendimientos").value);

function mostrarMensaje(texto) {
    document.getElementById("textoMensajeEmprendimientos").textContent = texto;
    document.getElementById("mensajeEmprendimientos").style.display = "flex";
}

// ---------- 2. Buscar y filtrar ----------
function filtrarEmprendimientos() {
    const texto = document.getElementById("buscarEmprendimiento").value.trim().toLowerCase();
    const provincia = document.getElementById("filtroProvincia").value;
    let visibles = 0;

    for (let i = 0; i < totalEmprendimientos; i++) {
        const datos = document.getElementById("busqueda-" + i).value.toLowerCase();
        const provinciaFila = document.getElementById("provincia-" + i).value;
        let mostrar = true;

        if (texto !== "" && !datos.includes(texto)) {
            mostrar = false;
        }
        if (provincia !== "todas" && provinciaFila !== provincia) {
            mostrar = false;
        }

        if (mostrar) {
            document.getElementById("emprendimiento-" + i).style.display = "flex";
            visibles++;
        } else {
            document.getElementById("emprendimiento-" + i).style.display = "none";
        }
    }

    if (visibles === 0) {
        document.getElementById("sinResultados").style.display = "block";
    } else {
        document.getElementById("sinResultados").style.display = "none";
    }
}

// keyup: se dispara cada vez que se suelta una tecla dentro del buscador
document.getElementById("buscarEmprendimiento").addEventListener("keyup", filtrarEmprendimientos);
document.getElementById("filtroProvincia").addEventListener("change", filtrarEmprendimientos);

// Si se llegó desde el buscador de la barra superior, se filtra de una vez
if (document.getElementById("buscarEmprendimiento").value !== "") {
    filtrarEmprendimientos();
}

// El botón "Filtros" muestra u oculta el filtro por provincia
document.getElementById("botonFiltros").addEventListener("click", function () {
    const panel = document.getElementById("panelFiltros");

    if (panel.style.display === "flex") {
        panel.style.display = "none";
    } else {
        panel.style.display = "flex";
    }
});

// ---------- 3. Suspender / reactivar ----------
function cambiarEstado(indice) {
    const nombre = document.getElementById("tienda-" + indice).textContent;
    const activo = document.getElementById("activo-" + indice);
    const boton = document.getElementById("estado-" + indice);
    const contador = document.getElementById("contadorActivos");

    if (activo.value === "1") {
        // Suspender: la vitrina deja de verse, pero no se borra nada
        activo.value = "0";
        boton.textContent = "Reactivar";
        boton.className = "ad-boton ad-boton--exito";
        document.getElementById("suspendido-" + indice).style.display = "inline-block";
        document.getElementById("sello-" + indice).style.display = "none";
        document.getElementById("emprendimiento-" + indice).className = "ad-fila ad-fila--suspendida";
        contador.textContent = Number(contador.textContent) - 1;
        mostrarMensaje(nombre + " fue suspendido. Su vitrina ya no aparece en el catálogo y se conserva su historial.");
    } else {
        activo.value = "1";
        boton.textContent = "Suspender";
        boton.className = "ad-boton ad-boton--peligro";
        document.getElementById("suspendido-" + indice).style.display = "none";
        document.getElementById("sello-" + indice).style.display = "flex";
        document.getElementById("emprendimiento-" + indice).className = "ad-fila";
        contador.textContent = Number(contador.textContent) + 1;
        mostrarMensaje(nombre + " fue reactivado y su vitrina vuelve a estar visible.");
    }
}

for (let i = 0; i < totalEmprendimientos; i++) {
    document.getElementById("estado-" + i).addEventListener("click", function () {
        if (MODO_DEMO) {
            cambiarEstado(i);
            return;
        }

        const nombre = document.getElementById("tienda-" + i).textContent;
        const activo = document.getElementById("activo-" + i).value === "1";
        let pregunta = "¿Reactivar " + nombre + "?";
        if (activo) {
            pregunta = "¿Suspender " + nombre + "? Su vitrina dejará de verse en el catálogo.";
        }

        if (confirm(pregunta)) {
            document.getElementById("idEmprendimientoEstado").value = document.getElementById("id-" + i).value;
            document.getElementById("formEstadoEmprendimiento").submit();
        }
    });
}

// ---------- 4. Exportar reporte ----------
// La impresión del navegador permite "Guardar como PDF"
document.getElementById("botonExportar").addEventListener("click", function () {
    window.print();
});
