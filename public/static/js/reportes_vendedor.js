/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · reportes_vendedor.js
   Pantalla Vendedor | Reportes (RF-13, HU-13)
   1. Variables
   2. Revisar el rango de fechas
   3. Enviar el formulario
   Usa marcarError() de validaciones.js (se carga antes).
   El servidor (ReporteService::validarRango) vuelve a validar igual.
   ========================================================== */

// ---------- 1. Variables ----------

const formRango = document.getElementById("formRango");
const campoDesde = document.getElementById("desde");
const campoHasta = document.getElementById("hasta");
const errorDesde = document.getElementById("errorDesde");
const errorHasta = document.getElementById("errorHasta");

// Días máximos del rango (igual que ReporteService::RANGO_MAXIMO_DIAS)
const RANGO_MAXIMO_DIAS = 366;
const MILISEGUNDOS_POR_DIA = 24 * 60 * 60 * 1000;

// ---------- 2. Revisar el rango ----------

// "2026-10-31" -> fecha a las 12:00 (así el cambio de hora no altera el conteo de días)
function aFecha(texto) {
    const partes = texto.split("-");
    return new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]), 12);
}

// Devuelve el mensaje de error de cada campo ("" si está bien)
function revisarRango() {
    const errores = { desde: "", hasta: "" };
    const hoy = campoDesde.max;   // el servidor deja hoy como fecha máxima

    if (campoDesde.value === "") {
        errores.desde = "Indicá la fecha inicial.";
    }
    if (campoHasta.value === "") {
        errores.hasta = "Indicá la fecha final.";
    }
    if (errores.desde !== "" || errores.hasta !== "") {
        return errores;
    }

    const dias = Math.round((aFecha(campoHasta.value) - aFecha(campoDesde.value)) / MILISEGUNDOS_POR_DIA) + 1;

    if (campoDesde.value > campoHasta.value) {
        errores.hasta = "La fecha final no puede ser anterior a la inicial.";
    } else if (campoHasta.value > hoy) {
        errores.hasta = "La fecha final no puede ser futura.";
    } else if (dias > RANGO_MAXIMO_DIAS) {
        errores.hasta = "El rango máximo es de 1 año (" + RANGO_MAXIMO_DIAS + " días).";
    }
    return errores;
}

// Pinta los errores en los campos (o los quita)
function mostrarErrores(errores) {
    errorDesde.textContent = errores.desde;
    errorHasta.textContent = errores.hasta;
    marcarError("desde", errores.desde !== "");
    marcarError("hasta", errores.hasta !== "");
}

// ---------- 3. Enviar el formulario ----------

formRango.addEventListener("submit", function (evento) {
    const errores = revisarRango();
    mostrarErrores(errores);

    if (errores.desde !== "" || errores.hasta !== "") {
        evento.preventDefault();
        if (errores.desde !== "") {
            campoDesde.focus();
        } else {
            campoHasta.focus();
        }
    }
});

// Al cambiar una fecha se quitan los errores que ya no apliquen
campoDesde.addEventListener("change", function () {
    mostrarErrores(revisarRango());
});

campoHasta.addEventListener("change", function () {
    mostrarErrores(revisarRango());
});