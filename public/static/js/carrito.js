/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · carrito.js
   1. Botones - y + : cambian la cantidad y recalculan totales.
   2. Quitar un producto del carrito.
   3. Forma de pago: resalta la opción y muestra el número SINPE.
   4. Copiar el número SINPE.
   5. Validar los datos de entrega antes de enviar.
   El servidor (CarritoController) vuelve a calcular todo:
   esto es solo para que la persona vea el total al instante.
   ========================================================== */

const formCarrito = document.getElementById("formCarrito");

// Formato de colones igual al de PHP: 30200 -> ₡30,200
function formatoColones(monto) {
    return "₡" + monto.toLocaleString("en-US");
}

// Devuelve el valor de la forma de pago escogida: sinpe, tarjeta o efectivo
function pagoEscogido() {
    const opciones = document.getElementsByName("pago");

    for (let i = 0; i < opciones.length; i++) {
        if (opciones[i].checked) {
            return opciones[i].value;
        }
    }
    return "sinpe";
}

// Recalcula subtotales, total y contadores con lo que hay en pantalla
function recalcular() {
    const items = document.getElementsByClassName("item-carrito");
    const costoEnvio = Number(formCarrito.getAttribute("data-envio"));
    let subtotal = 0;
    let unidades = 0;

    for (let i = 0; i < items.length; i++) {
        const precio = Number(items[i].getAttribute("data-precio"));
        const entrada = items[i].getElementsByClassName("cantidad__entrada")[0];
        let cantidad = Number(entrada.value);

        // La cantidad debe estar entre 1 y 99
        if (cantidad < 1) {
            cantidad = 1;
        }
        if (cantidad > 99) {
            cantidad = 99;
        }
        entrada.value = cantidad;

        items[i].getElementsByClassName("item-carrito__subtotal")[0].textContent = formatoColones(precio * cantidad);
        subtotal = subtotal + precio * cantidad;
        unidades = unidades + cantidad;
    }

    const total = subtotal + costoEnvio;

    document.getElementById("montoSubtotal").textContent = formatoColones(subtotal);
    document.getElementById("montoTotal").textContent = formatoColones(total);
    document.getElementById("conteoUnidades").textContent = unidades;
    document.getElementById("conteoProductos").textContent = items.length;
    document.getElementById("cantidadVitrinas").textContent = document.getElementsByClassName("vitrina").length;

    // Texto del botón según la forma de pago
    let textoPago = "con SINPE";
    if (pagoEscogido() === "tarjeta") {
        textoPago = "con tarjeta";
    } else if (pagoEscogido() === "efectivo") {
        textoPago = "contra entrega";
    }
    document.getElementById("textoPagar").textContent = "Confirmar y pagar " + formatoColones(total) + " " + textoPago;
}

if (formCarrito) {

    // ---------- 1. Botones - y + ----------
    const botonesCantidad = document.getElementsByClassName("cantidad__boton");

    for (let i = 0; i < botonesCantidad.length; i++) {
        botonesCantidad[i].addEventListener("click", function () {
            // "this" es el botón que se tocó. El input está en el mismo div.
            const entrada = this.parentElement.getElementsByClassName("cantidad__entrada")[0];
            const cambio = Number(this.getAttribute("data-cambio"));   // -1 o 1

            entrada.value = Number(entrada.value) + cambio;
            recalcular();
        });
    }

    // Si la persona escribe la cantidad a mano
    const entradasCantidad = document.getElementsByClassName("cantidad__entrada");

    for (let i = 0; i < entradasCantidad.length; i++) {
        entradasCantidad[i].addEventListener("change", recalcular);
    }

    // ---------- 2. Quitar producto ----------
    // Por ahora se quita de la pantalla. Cuando exista CarritoController
    // se envía el id del producto a la ruta carrito/quitar.
    const botonesQuitar = document.getElementsByClassName("item-carrito__quitar");

    for (let i = 0; i < botonesQuitar.length; i++) {
        botonesQuitar[i].addEventListener("click", function () {
            // Se usa "this" (el botón tocado) y no botonesQuitar[i], porque al quitar
            // productos la lista de botones cambia de tamaño y los números se corren.

            // Se avisa al servidor (CarritoController::quitar) con el formulario escondido
            document.getElementById("idQuitar").value = this.getAttribute("data-id");
            document.getElementById("formQuitar").submit();

            const item = this.parentElement;                      // el <li> del producto
            const vitrina = item.parentElement.parentElement;      // <li> -> <ul> -> <section> del emprendimiento

            item.remove();

            // Si el emprendimiento se quedó sin productos, se quita el grupo completo
            if (vitrina.getElementsByClassName("item-carrito").length === 0) {
                vitrina.remove();
            }

            // Si ya no quedan productos, se muestra el mensaje de carrito vacío
            if (document.getElementsByClassName("item-carrito").length === 0) {
                formCarrito.hidden = true;
                document.getElementById("carritoVacio").hidden = false;
            } else {
                recalcular();
            }
        });
    }

    // ---------- 3. Forma de pago ----------
    const opcionesPago = document.getElementsByName("pago");

    for (let i = 0; i < opcionesPago.length; i++) {
        opcionesPago[i].addEventListener("change", function () {
            // Primero se quita el resaltado de todas...
            for (let j = 0; j < opcionesPago.length; j++) {
                opcionesPago[j].parentElement.classList.remove("es-marcado");
            }
            // ...y se resalta solo la escogida
            opcionesPago[i].parentElement.classList.add("es-marcado");

            // El número SINPE solo se muestra si se escogió SINPE
            document.getElementById("detalleSinpe").hidden = opcionesPago[i].value !== "sinpe";

            recalcular();
        });
    }

    // ---------- 4. Copiar número SINPE ----------
    const botonCopiar = document.getElementById("copiarSinpe");

    botonCopiar.addEventListener("click", function () {
        const numero = botonCopiar.getAttribute("data-numero");
        navigator.clipboard.writeText(numero);
        alert("Número SINPE copiado: " + numero);
    });

    // ---------- 5. Validación antes de enviar ----------
    formCarrito.addEventListener("submit", function (evento) {
        let hayErrores = false;

        const nombre = document.getElementById("nombre").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const provincia = document.getElementById("provincia").value;
        const canton = document.getElementById("canton").value;
        const distrito = document.getElementById("distrito").value;
        const direccion = document.getElementById("direccion").value.trim();

        if (nombre.length >= 5) {
            marcarError("nombre", false);
        } else {
            marcarError("nombre", true);
            hayErrores = true;
        }

        if (telefonoValido(telefono)) {
            marcarError("telefono", false);
        } else {
            marcarError("telefono", true);
            hayErrores = true;
        }

        if (provincia !== "") {
            marcarError("provincia", false);
        } else {
            marcarError("provincia", true);
            hayErrores = true;
        }

        if (distrito !== "") {
            marcarError("distrito", false);
        } else {
            marcarError("distrito", true);
            hayErrores = true;
        }

        if (canton !== "") {
            marcarError("canton", false);
        } else {
            marcarError("canton", true);
            hayErrores = true;
        }

        if (direccion.length >= 15) {
            marcarError("direccion", false);
        } else {
            marcarError("direccion", true);
            hayErrores = true;
        }

        // Con SINPE el comprobante es opcional, pero si se escribe debe tener 4 caracteres o más
        if (pagoEscogido() === "sinpe") {
            const comprobante = document.getElementById("comprobante").value.trim();

            if (comprobante === "" || comprobante.length >= 4) {
                marcarError("comprobante", false);
            } else {
                marcarError("comprobante", true);
                hayErrores = true;
            }
        }

        // preventDefault() detiene el envío del formulario
        if (hayErrores) {
            evento.preventDefault();
        }
    });

    recalcular();
}
