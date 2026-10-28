/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · solicitudes_admin.js
   Pantalla Admin | Emprendimientos Pendientes (RF-03)
   1. Variables y mensaje
   2. Pestañas (todas, nuevas, con observaciones)
   3. Descargar reporte CSV
   ========================================================== */

// ---------- 1. Variables y mensaje ----------

// Cada fila tiene id "solicitud-0", "solicitud-1"...
const totalSolicitudes = Number(document.getElementById("totalSolicitudes").value);

function mostrarMensaje(texto) {
    document.getElementById("textoMensajeSolicitudes").textContent = texto;
    document.getElementById("mensajeSolicitudes").style.display = "flex";
}

// ---------- 2. Pestañas ----------
// filtro puede ser: "todas", "nueva" u "observacion" (igual que el campo "tipo" de la fila)
function filtrarSolicitudes(filtro, idPestana) {
    let visibles = 0;

    for (let i = 0; i < totalSolicitudes; i++) {
        const tipo = document.getElementById("tipo-" + i).value;

        if (filtro === "todas" || tipo === filtro) {
            document.getElementById("solicitud-" + i).style.display = "flex";
            visibles++;
        } else {
            document.getElementById("solicitud-" + i).style.display = "none";
        }
    }

    // Marca la pestaña activa y desmarca las otras
    const pestanas = ["pestanaTodas", "pestanaNuevas", "pestanaObservaciones"];
    for (let i = 0; i < pestanas.length; i++) {
        const pestana = document.getElementById(pestanas[i]);
        if (pestanas[i] === idPestana) {
            pestana.classList.add("es-activo");
            pestana.setAttribute("aria-selected", "true");
        } else {
            pestana.classList.remove("es-activo");
            pestana.setAttribute("aria-selected", "false");
        }
    }

    if (visibles === 0) {
        document.getElementById("sinResultados").style.display = "block";
    } else {
        document.getElementById("sinResultados").style.display = "none";
    }

    document.getElementById("contadorFilas").textContent =
        "Mostrando " + visibles + " solicitudes pendientes para dictamen del comité de calidad";
}

document.getElementById("pestanaTodas").addEventListener("click", function () {
    filtrarSolicitudes("todas", "pestanaTodas");
});
document.getElementById("pestanaNuevas").addEventListener("click", function () {
    filtrarSolicitudes("nueva", "pestanaNuevas");
});
document.getElementById("pestanaObservaciones").addEventListener("click", function () {
    filtrarSolicitudes("observacion", "pestanaObservaciones");
});

// ---------- 3. Descargar reporte CSV ----------
// Arma el archivo con las filas que se ven en pantalla (respeta la pestaña elegida).
function textoCsv(valor) {
    // Se rodea con comillas por si el texto trae comas
    return '"' + valor.replace(/"/g, '""') + '"';
}

document.getElementById("botonCsv").addEventListener("click", function () {
    let csv = "Emprendimiento,Expediente,Categoria,Ubicacion,Fecha,Estado de documentos\n";
    let filas = 0;

    for (let i = 0; i < totalSolicitudes; i++) {
        if (document.getElementById("solicitud-" + i).style.display === "none") {
            continue;
        }

        csv += textoCsv(document.getElementById("tienda-" + i).textContent.trim()) + ",";
        csv += textoCsv(document.getElementById("expediente-" + i).textContent.replace("Expediente", "").trim()) + ",";
        csv += textoCsv(document.getElementById("categoria-" + i).value) + ",";
        csv += textoCsv(document.getElementById("ubicacion-" + i).value) + ",";
        csv += textoCsv(document.getElementById("fecha-" + i).value) + ",";
        csv += textoCsv(document.getElementById("nota-" + i).textContent.trim()) + "\n";
        filas++;
    }

    // "﻿" hace que Excel lea bien las tildes
    const archivo = new Blob(["﻿" + csv], { type: "text/csv;charset=utf-8;" });
    const enlace = document.createElement("a");
    enlace.href = URL.createObjectURL(archivo);
    enlace.download = "emprendimientos_pendientes.csv";
    document.body.appendChild(enlace);
    enlace.click();
    document.body.removeChild(enlace);
    URL.revokeObjectURL(enlace.href);

    mostrarMensaje("Reporte descargado: " + filas + " solicitudes en el archivo CSV.");
});
