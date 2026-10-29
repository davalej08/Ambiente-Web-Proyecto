/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · solicitud_detalle.js
   Pantalla Admin | Detalle de Solicitud (RF-03)
   1. Variables
   2. Validar y resolver la solicitud (aprobar o rechazar)
   Usa marcarError() de validaciones.js (se carga antes).
   ========================================================== */

// ---------- 1. Variables ----------

// MODO_DEMO en true: la resolución solo se refleja en la pantalla (sin guardar).
// En false: el formulario se envía a AdminController y se guarda en la base de datos.
const MODO_DEMO = false;

const formDictamen = document.getElementById("formDictamen");
const campoObservaciones = document.getElementById("observaciones");
const botonAprobar = document.getElementById("botonAprobar");
const botonRechazar = document.getElementById("botonRechazar");

// ---------- 2. Validar y resolver ----------

// Para rechazar, el motivo es obligatorio (mínimo 10 caracteres)
function observacionValida(decision) {
    if (decision === "aprobar") {
        return true;
    }
    return campoObservaciones.value.trim().length >= 10;
}

function resolverSolicitud(decision) {
    const nombre = document.getElementById("nombreEmprendimiento").value;
    const valida = observacionValida(decision);

    marcarError("observaciones", !valida);
    if (!valida) {
        campoObservaciones.focus();
        return;
    }

    let pregunta = "¿Aprobar el emprendimiento " + nombre + "?";
    if (decision === "rechazar") {
        pregunta = "¿Rechazar la solicitud de " + nombre + "?";
    }
    if (!window.confirm(pregunta)) {
        return;
    }

    document.getElementById("decision").value = decision;

    if (!MODO_DEMO) {
        formDictamen.submit();
        return;
    }

    // Modo demo: se muestra el resultado sin enviar nada
    const estado = document.getElementById("estadoSolicitud");
    const mensaje = document.getElementById("mensajeDictamen");

    if (decision === "aprobar") {
        estado.textContent = "Aprobada";
        document.getElementById("textoMensajeDictamen").textContent =
            nombre + " fue aprobado. Se notificará al emprendedor por correo y WhatsApp.";
        mensaje.classList.remove("mensaje--error");
        mensaje.classList.add("mensaje--exito");
    } else {
        estado.textContent = "Rechazada";
        document.getElementById("textoMensajeDictamen").textContent =
            "La solicitud de " + nombre + " fue rechazada. El motivo se enviará al emprendedor.";
        mensaje.classList.remove("mensaje--exito");
        mensaje.classList.add("mensaje--error");
    }

    mensaje.style.display = "flex";
    botonAprobar.disabled = true;
    botonRechazar.disabled = true;
    campoObservaciones.disabled = true;
    mensaje.scrollIntoView({ behavior: "smooth", block: "center" });
}

botonAprobar.addEventListener("click", function () {
    resolverSolicitud("aprobar");
});

botonRechazar.addEventListener("click", function () {
    resolverSolicitud("rechazar");
});

// Al escribir se quita el error
campoObservaciones.addEventListener("keyup", function () {
    if (campoObservaciones.value.trim().length >= 10) {
        marcarError("observaciones", false);
    }
});
