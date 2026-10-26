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
   Por ahora los cambios son visuales (no se guardan). Cuando
   exista el controlador se quita el preventDefault() de la
   sección 2 para que los formularios se envíen.
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

    // Texto y color de cada estado (igual que en pedidos.php)
    const ESTADOS = {
        confirmado: { texto: "Confirmado y Pagado", clase: "estado-pedido--oscuro", grupo: "listos" },
        entregado: { texto: "Entregado con éxito", clase: "estado-pedido--verde", grupo: "listos" }
    };

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

    // Cuenta los pedidos de cada grupo y actualiza los números de arriba
    function actualizarConteos() {
        let pendientes = 0;
        let listos = 0;

        for (let i = 0; i < tarjetas.length; i++) {
            if (tarjetas[i].getAttribute("data-grupo") === "pendientes") {
                pendientes++;
            } else {
                listos++;
            }
        }

        document.querySelector('[data-conteo="todos"]').textContent = "(" + tarjetas.length + ")";
        document.querySelector('[data-conteo="pendientes"]').textContent = "(" + pendientes + ")";
        document.querySelector('[data-conteo="listos"]').textContent = "(" + listos + ")";
        document.getElementById("datoPorDespachar").textContent = pendientes + (pendientes === 1 ? " envío" : " envíos");
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
            evento.preventDefault();

            const form = formsPedido[i];
            const tarjeta = form.closest(".pedido");
            const cliente = tarjeta.getElementsByTagName("h3")[0].textContent;
            const accion = form.getAttribute("data-accion");

            if (accion === "confirmar") {
                if (confirm("¿Confirmar el pedido de " + cliente + "?")) {
                    cambiarEstado(tarjeta, "confirmado");

                    // El mismo formulario pasa a ser "Marcar como entregado"
                    form.setAttribute("data-accion", "entregar");
                    form.setAttribute("action", form.getAttribute("action").replace("confirmar", "entregar"));
                    form.getElementsByTagName("button")[0].className = "btn btn--primario btn--sm";
                    form.getElementsByTagName("span")[0].textContent = "Marcar como entregado";
                    form.getElementsByTagName("use")[0].setAttribute("href",
                        form.getElementsByTagName("use")[0].getAttribute("href").replace("#i-check", "#i-camion"));
                }
            } else {
                if (confirm("¿Marcar como entregado el pedido de " + cliente + "?")) {
                    cambiarEstado(tarjeta, "entregado");

                    // El botón se reemplaza por una nota
                    const nota = document.createElement("p");
                    nota.className = "pedido__nota";
                    nota.textContent = "Notificado al cliente";
                    form.parentElement.replaceChild(nota, form);
                }
            }
        });
    }

    // Cambia la etiqueta y el grupo de una tarjeta y refresca los números
    function cambiarEstado(tarjeta, nuevoEstado) {
        const etiqueta = tarjeta.getElementsByClassName("estado-pedido")[0];

        tarjeta.setAttribute("data-estado", nuevoEstado);
        tarjeta.setAttribute("data-grupo", ESTADOS[nuevoEstado].grupo);

        etiqueta.className = "estado-pedido " + ESTADOS[nuevoEstado].clase;
        etiqueta.innerHTML = '<span class="punto"></span> ' + ESTADOS[nuevoEstado].texto;

        actualizarConteos();
        aplicarFiltros();
    }

    // ---------- 3. Pedido resaltado desde el dashboard ----------
    // El dashboard enlaza a vendedor/pedidos?id=8491
    const idBuscado = new URLSearchParams(window.location.search).get("id");

    if (idBuscado) {
        const tarjetaBuscada = document.getElementById("pedido-" + idBuscado);

        if (tarjetaBuscada) {
            tarjetaBuscada.classList.add("es-resaltado");
            tarjetaBuscada.scrollIntoView({ block: "center" });
        }
    }
}
