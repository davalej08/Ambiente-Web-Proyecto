/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ============================================================
   Selects de provincia, cantón y distrito en cascada
   (vista general/ubicacion.php)

   Al cambiar la provincia se muestran solo sus cantones;
   al cambiar el cantón, solo sus distritos. Las listas completas
   están en los selects ocultos #todosCantones y #todosDistritos.
   ============================================================ */

// Deja en "destino" solo las opciones de "origen" cuyo data-padre sea "idPadre"
function llenarSelect(destino, origen, idPadre) {
    // Se borran todas menos la primera ("Elegí")
    while (destino.options.length > 1) {
        destino.remove(1);
    }

    for (let i = 0; i < origen.options.length; i++) {
        const opcion = origen.options[i];

        if (opcion.getAttribute("data-padre") === idPadre) {
            const nueva = document.createElement("option");
            nueva.value = opcion.value;
            nueva.textContent = opcion.textContent;
            destino.appendChild(nueva);
        }
    }

    destino.value = "";
}

const selectProvincia = document.getElementById("provincia");
const selectCanton = document.getElementById("canton");
const selectDistrito = document.getElementById("distrito");
const todosCantones = document.getElementById("todosCantones");
const todosDistritos = document.getElementById("todosDistritos");

if (selectProvincia && selectCanton && selectDistrito && todosCantones && todosDistritos) {

    selectProvincia.addEventListener("change", function () {
        llenarSelect(selectCanton, todosCantones, selectProvincia.value);
        llenarSelect(selectDistrito, todosDistritos, "");
    });

    selectCanton.addEventListener("change", function () {
        llenarSelect(selectDistrito, todosDistritos, selectCanton.value);
    });
}
