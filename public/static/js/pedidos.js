/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/* ==========================================================
   VENTICA · pedidos.js
   Interacciones de Gestión de Pedidos (portal del emprendedor):
   1. Filtros: pestañas por estado, buscador y chips.
   2. Confirmar pedido / Marcar como entregado.
   3. Resaltar el pedido que viene del dashboard (?id=...).
   Los formularios de la sección 2 se envían a VendedorController
   (confirmar y entregar) después de preguntar.
   ========================================================== */

const listaPedidos = document.getElementById("listaPedidos");

if (listaPedidos) {
    const tarjetas = listaPedidos.getElementsByClassName("pedido");
    const buscador = document.getElementById("buscadorPedidos");
    const sinResultados = document.getElementById("sinResultados");
    const botonesGrupo = document.getElementsByClassName("pedidos-pestanas__boton");
    const chips = document.getElementsByClassName("pedidos-filtros__chip");

    // Lo que el usuario tiene escogido en este momento
    let grupoActivo = "todos";

    // ---------- 1. Filtros ----------

    // Un chip está activo si su aria-pressed es "true"
    function chipActivo(nombre) {
        for (let i = 0; i < chips.length; i++) {
            if (chips[i].getAttribute("data-filtro") === nombre) {
                return chips[i].getAttribute("aria-pressed") === "true";
            }
        }
        return false;
    }

    // Muestra u oculta cada tarjeta según pestaña, búsqueda y chips
    function aplicarFiltros() {
        const texto = buscador.value.trim().toLowerCase();
        let visibles = 0;

        for (let i = 0; i < tarjetas.length; i++) {
            const t = tarjetas[i];
            let mostrar = true;

            if (grupoActivo !== "todos" && t.getAttribute("data-grupo") !== grupoActivo) {
                mostrar = false;
            }
            if (texto !== "" && t.getAttribute("data-buscar").indexOf(texto) === -1) {
                mostrar = false;
            }
            if (chipActivo("hoy") && t.getAttribute("data-hoy") !== "1") {
                mostrar = false;
            }
            if (chipActivo("sinpe") && t.getAttribute("data-pago") !== "sinpe") {
                mostrar = false;
            }
            if (chipActivo("gam") && t.getAttribute("data-entrega") !== "gam") {
                mostrar = false;
            }

            t.hidden = !mostrar;
            if (mostrar) {
                visibles++;
            }
        }

        sinResultados.hidden = visibles > 0;
    }

    // Pestañas Todos / Pendientes / Listos
    for (let i = 0; i < botonesGrupo.length; i++) {
        botonesGrupo[i].addEventListener("click", function () {
            grupoActivo = botonesGrupo[i].getAttribute("data-grupo");

            for (let j = 0; j < botonesGrupo.length; j++) {
                botonesGrupo[j].setAttribute("aria-pressed", j === i ? "true" : "false");
            }
            aplicarFiltros();
        });
    }

    // Chips Recientes / SINPE Móvil / GAM Express: se prenden y se apagan
    for (let i = 0; i < chips.length; i++) {
        chips[i].addEventListener("click", function () {
            const activo = chips[i].getAttribute("aria-pressed") === "true";
            chips[i].setAttribute("aria-pressed", activo ? "false" : "true");
            aplicarFiltros();
        });
    }

    buscador.addEventListener("input", aplicarFiltros);

    // ---------- 2. Confirmar y entregar ----------
    const formsPedido = document.getElementsByClassName("form-pedido");

    for (let i = 0; i < formsPedido.length; i++) {
        formsPedido[i].addEventListener("submit", function (evento) {
            const form = formsPedido[i];
            // form -> div.pedido__acciones -> div.pedido__estado-acciones -> li.pedido
            const tarjeta = form.parentElement.parentElement.parentElement;
            const cliente = tarjeta.getElementsByTagName("h3")[0].textContent;
            const accion = form.getAttribute("data-accion");

            let pregunta = "¿Confirmar el pedido de " + cliente + "?";
            if (accion === "entregar") {
                pregunta = "¿Marcar como entregado el pedido de " + cliente + "?";
            }

            // Si la persona cancela, no se envía. Si acepta, el formulario va a
            // VendedorController y la página vuelve con el estado nuevo.
            if (!confirm(pregunta)) {
                evento.preventDefault();
            }
        });
    }

    // ---------- 3. Pedido resaltado desde el dashboard ----------
    // El dashboard enlaza a vendedor/pedidos?id=8491; PHP deja ese número en #pedidoResaltado
    const idBuscado = document.getElementById("pedidoResaltado").value;

    if (idBuscado !== "0") {
        const tarjetaBuscada = document.getElementById("pedido-" + idBuscado);

        if (tarjetaBuscada) {
            tarjetaBuscada.classList.add("es-resaltado");
            tarjetaBuscada.scrollIntoView({ block: "center" });
        }
    }
}
