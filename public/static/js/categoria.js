/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · categoria.js
   Pantalla Admin | Gestión de Categorías 
   1. Variables y funciones de apoyo
   2. Mostrar / ocultar el formulario
   3. Agregar y editar categorías
   4. Eliminar (solo si no tiene productos)
   5. Filtros por actividad
   Usa marcarError() de validaciones.js (se carga antes).
   ========================================================== */

// ---------- 1. Variables y funciones de apoyo ----------

// MODO_DEMO en true: los cambios solo se ven en la pantalla (sin guardar).
// En false: los formularios se envían a CategoriaController y se guardan en la base de datos.
const MODO_DEMO = false;

// Cuántas tarjetas generó PHP. Cada una tiene id "categoria-0", "categoria-1", ...
let totalCategorias = Number(document.getElementById("totalCategorias").value);

// Filtro que está activo: "todas", "mayor" o "artesanales"
let filtroActual = "todas";

// Muestra el mensaje de arriba. tipo: "exito" o "error"
function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById("mensajeCategoria");
    document.getElementById("textoMensajeCategoria").textContent = texto;

    if (tipo === "error") {
        mensaje.className = "mensaje mensaje--error";
    } else {
        mensaje.className = "mensaje mensaje--exito";
    }
    mensaje.style.display = "flex";
}

// Cuenta las tarjetas que siguen en la página y actualiza los contadores
function actualizarContador() {
    let cantidad = 0;

    for (let i = 0; i < totalCategorias; i++) {
        if (document.getElementById("categoria-" + i) !== null) {
            cantidad++;
        }
    }

    document.getElementById("contadorCategorias").textContent = cantidad;
    document.getElementById("contadorFiltro").textContent = cantidad;
}

// Revisa si ya existe una categoría con ese nombre (sin importar mayúsculas)
// indiceIgnorar: la categoría que se está editando, para no compararla consigo misma
function nombreRepetido(nombre, indiceIgnorar) {
    for (let i = 0; i < totalCategorias; i++) {
        const elementoNombre = document.getElementById("nombre-" + i);

        if (elementoNombre !== null && i !== indiceIgnorar) {
            if (elementoNombre.textContent.toLowerCase() === nombre.toLowerCase()) {
                return true;
            }
        }
    }
    return false;
}

// ---------- 2. Mostrar / ocultar el formulario ----------
function abrirFormulario(indice) {
    const panel = document.getElementById("panelFormulario");
    marcarError("nombreCategoria", false);
    document.getElementById("indiceEdicion").value = indice;

    // Id real de la categoría para el servidor (0 = nueva)
    if (indice === -1) {
        document.getElementById("idCategoriaEdicion").value = "0";
    } else {
        document.getElementById("idCategoriaEdicion").value = document.getElementById("id-" + indice).value;
    }

    if (indice === -1) {
        // Categoría nueva: el formulario va vacío
        document.getElementById("tituloFormulario").textContent = "Nueva categoría";
        document.getElementById("textoGuardar").textContent = "Agregar categoría";
        document.getElementById("nombreCategoria").value = "";
        document.getElementById("descripcionCategoria").value = "";
        document.getElementById("tipoCategoria").value = "1";
    } else {
        // Editar: se copian los datos de la tarjeta al formulario
        document.getElementById("tituloFormulario").textContent = "Editar categoría";
        document.getElementById("textoGuardar").textContent = "Guardar cambios";
        document.getElementById("nombreCategoria").value = document.getElementById("nombre-" + indice).textContent;
        document.getElementById("descripcionCategoria").value = document.getElementById("descripcion-" + indice).textContent;
        document.getElementById("tipoCategoria").value = document.getElementById("artesanal-" + indice).value;
    }

    panel.style.display = "block";
    document.getElementById("nombreCategoria").focus();
}

function cerrarFormulario() {
    document.getElementById("panelFormulario").style.display = "none";
    document.getElementById("indiceEdicion").value = "-1";
}

document.getElementById("botonNuevaCategoria").addEventListener("click", function () {
    abrirFormulario(-1);
});

document.getElementById("cancelarCategoria").addEventListener("click", cerrarFormulario);

// ---------- 3. Agregar y editar ----------

// Arma una tarjeta nueva (igual a las que genera PHP) y la agrega al final
function crearTarjeta(nombre, descripcion, artesanal) {
    const indice = totalCategorias;
    let numero = indice + 1;
    if (numero < 10) {
        numero = "0" + numero;
    }

    const tarjeta = document.createElement("article");
    tarjeta.className = "ad-categoria";
    tarjeta.id = "categoria-" + indice;
    tarjeta.innerHTML =
        '<div class="ad-categoria__arriba">' +
            '<span class="ad-categoria__icono">' + document.getElementById("iconoNueva").innerHTML + '</span>' +
            '<div>' +
                '<h3><span id="nombre-' + indice + '"></span> <span class="ad-chip ad-chip--suave">#' + numero + '</span></h3>' +
                '<p id="descripcion-' + indice + '"></p>' +
            '</div>' +
        '</div>' +
        '<div class="ad-categoria__datos">' +
            '<span>' + document.getElementById("iconoProductos").innerHTML + ' <strong id="productosTexto-' + indice + '">0</strong> productos</span>' +
            '<span>' + document.getElementById("iconoMarcas").innerHTML + ' <strong>0</strong> marcas ticas</span>' +
        '</div>' +
        '<div class="ad-categoria__pie">' +
            '<span class="ad-en-vivo"><span class="punto"></span> Visible en vitrina</span>' +
            '<div class="solo-pantalla">' +
                '<button class="ad-boton ad-boton--azul" type="button" id="editar-' + indice + '">' + document.getElementById("iconoEditar").innerHTML + ' Editar</button>' +
                '<button class="ad-boton ad-boton--peligro" type="button" id="eliminar-' + indice + '">' + document.getElementById("iconoEliminar").innerHTML + ' Eliminar</button>' +
            '</div>' +
        '</div>' +
        '<input type="hidden" id="productos-' + indice + '" value="0">' +
        '<input type="hidden" id="artesanal-' + indice + '" value="' + artesanal + '">';

    document.getElementById("listaCategorias").appendChild(tarjeta);

    // textContent para que lo que escribió el usuario no se interprete como HTML
    document.getElementById("nombre-" + indice).textContent = nombre;
    document.getElementById("descripcion-" + indice).textContent = descripcion;

    totalCategorias++;
    document.getElementById("totalCategorias").value = totalCategorias;
    activarBotones(indice);
}

document.getElementById("guardarCategoria").addEventListener("click", function () {
    const nombre = document.getElementById("nombreCategoria").value.trim();
    let descripcion = document.getElementById("descripcionCategoria").value.trim();
    const artesanal = document.getElementById("tipoCategoria").value;
    const indice = Number(document.getElementById("indiceEdicion").value);

    // Validación: mínimo 3 letras
    if (nombre.length < 3) {
        marcarError("nombreCategoria", true);
        return;
    }
    marcarError("nombreCategoria", false);

    if (nombreRepetido(nombre, indice)) {
        mostrarMensaje("Ya existe una categoría llamada " + nombre + ".", "error");
        return;
    }

    if (descripcion === "") {
        descripcion = "Sin descripción.";
    }

    if (!MODO_DEMO) {
        document.getElementById("formCategoria").submit();
        return;
    }

    if (indice === -1) {
        crearTarjeta(nombre, descripcion, artesanal);
        actualizarContador();
        mostrarMensaje("La categoría " + nombre + " se agregó al catálogo.", "exito");
    } else {
        document.getElementById("nombre-" + indice).textContent = nombre;
        document.getElementById("descripcion-" + indice).textContent = descripcion;
        document.getElementById("artesanal-" + indice).value = artesanal;
        mostrarMensaje("Los cambios de " + nombre + " se guardaron.", "exito");
    }

    cerrarFormulario();
    aplicarFiltro();
});

// ---------- 4. Eliminar ----------
function eliminarCategoria(indice) {
    const nombre = document.getElementById("nombre-" + indice).textContent;
    const productos = Number(document.getElementById("productos-" + indice).value);

    // Regla de RF-10: una categoría con productos no se puede borrar
    if (productos > 0) {
        mostrarMensaje("No se puede eliminar " + nombre + " porque tiene " + productos + " productos. Primero hay que moverlos a otra categoría.", "error");
        return;
    }

    if (confirm("¿Eliminar la categoría " + nombre + "?")) {
        if (!MODO_DEMO) {
            document.getElementById("idCategoriaEliminar").value = document.getElementById("id-" + indice).value;
            document.getElementById("formEliminarCategoria").submit();
            return;
        }
        document.getElementById("categoria-" + indice).remove();
        actualizarContador();
        cerrarFormulario();
        mostrarMensaje("La categoría " + nombre + " se eliminó.", "exito");
    }
}

// Conecta los botones Editar y Eliminar de una tarjeta
function activarBotones(indice) {
    document.getElementById("editar-" + indice).addEventListener("click", function () {
        abrirFormulario(indice);
    });
    document.getElementById("eliminar-" + indice).addEventListener("click", function () {
        eliminarCategoria(indice);
    });
}

for (let i = 0; i < totalCategorias; i++) {
    activarBotones(i);
}

// ---------- 5. Filtros por actividad ----------
function aplicarFiltro() {
    for (let i = 0; i < totalCategorias; i++) {
        const tarjeta = document.getElementById("categoria-" + i);

        if (tarjeta !== null) {
            const productos = Number(document.getElementById("productos-" + i).value);
            const artesanal = document.getElementById("artesanal-" + i).value;
            let mostrar = true;

            switch (filtroActual) {
                case "mayor":
                    mostrar = productos >= 300;
                    break;
                case "artesanales":
                    mostrar = artesanal === "1";
                    break;
                default:
                    mostrar = true;
            }

            if (mostrar) {
                tarjeta.style.display = "flex";
            } else {
                tarjeta.style.display = "none";
            }
        }
    }
}

// Marca el botón tocado y aplica el filtro
function elegirFiltro(filtro, idBoton) {
    filtroActual = filtro;
    document.getElementById("filtroTodas").className = "ad-chip";
    document.getElementById("filtroMayor").className = "ad-chip";
    document.getElementById("filtroArtesanales").className = "ad-chip";
    document.getElementById(idBoton).className = "ad-chip es-activo";
    aplicarFiltro();
}

document.getElementById("filtroTodas").addEventListener("click", function () {
    elegirFiltro("todas", "filtroTodas");
});
document.getElementById("filtroMayor").addEventListener("click", function () {
    elegirFiltro("mayor", "filtroMayor");
});
document.getElementById("filtroArtesanales").addEventListener("click", function () {
    elegirFiltro("artesanales", "filtroArtesanales");
});
