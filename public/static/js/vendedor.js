/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */
/* ==========================================================
   VENTICA · vendedor.js
   Interacciones del portal del emprendedor:
   1. Dashboard: barra de la meta y botón Confirmar pedido.
   2. Mis Productos: interruptor Activo / Pausado.
   3. Agregar producto: vista previa de la foto, contador de
      caracteres y validación del formulario.
   4. Mi Perfil: vista previa del logo y validación.
   Las validaciones usan marcarError() y telefonoValido()
   de validaciones.js (se carga antes que este archivo).
   ========================================================== */

// Una foto es válida si es JPG, PNG o WEBP y pesa 5 MB o menos
function fotoValida(archivo) {
    const tipoValido = archivo.type === "image/jpeg" || archivo.type === "image/png" || archivo.type === "image/webp";
    const pesoValido = archivo.size <= 5 * 1024 * 1024;

    return tipoValido && pesoValido;
}

// ---------- 1. Dashboard ----------

// Barra de la meta: toma el porcentaje que manda PHP en data-avance
const barraMeta = document.getElementById("barraMeta");

if (barraMeta) {
    barraMeta.style.width = barraMeta.getAttribute("data-avance") + "%";
}

// Botón Confirmar pedido: pregunta antes de enviar el formulario a
// VendedorController::confirmarPedido. Si la persona cancela, no se envía.
const formsConfirmar = document.getElementsByClassName("form-confirmar");
let pedidosPendientes = formsConfirmar.length;

for (let i = 0; i < formsConfirmar.length; i++) {
    formsConfirmar[i].addEventListener("submit", function (evento) {
        // form -> div de acciones -> fila del pedido
        const fila = formsConfirmar[i].parentElement.parentElement;
        const cliente = fila.getElementsByTagName("h3")[0].textContent;

        if (!confirm("¿Confirmar el pedido de " + cliente + "?")) {
            evento.preventDefault();
        } else {
            fila.classList.add("es-confirmado");
            formsConfirmar[i].getElementsByTagName("button")[0].disabled = true;

            pedidosPendientes = pedidosPendientes - 1;
            document.getElementById("contadorPendientes").textContent = pedidosPendientes + " por procesar";
        }
    });
}

// ---------- 2. Mis Productos ----------

// Interruptor de disponibilidad: cambia el texto Activo / Pausado
const interruptores = document.getElementsByClassName("interruptor__entrada");

for (let i = 0; i < interruptores.length; i++) {
    interruptores[i].addEventListener("change", function () {
        // El texto está justo después del checkbox
        const texto = interruptores[i].nextElementSibling;

        if (interruptores[i].checked) {
            texto.textContent = "Activo";
        } else {
            texto.textContent = "Pausado";
        }

        // Se guarda el cambio (VendedorController::disponibilidad)
        interruptores[i].form.submit();
    });
}

// ---------- 3. Agregar producto ----------
const formProducto = document.getElementById("formProducto");

if (formProducto) {
    const entradaFoto = document.getElementById("imagen");
    const vistaFoto = document.getElementById("vistaFoto");
    const guiaFoto = document.getElementById("guiaFoto");
    const descripcion = document.getElementById("descripcion");
    const contador = document.getElementById("contadorDescripcion");

    // Vista previa de la foto escogida
    entradaFoto.addEventListener("change", function () {
        const archivo = entradaFoto.files[0];

        if (fotoValida(archivo)) {
            marcarError("imagen", false);
            vistaFoto.src = URL.createObjectURL(archivo);   // dirección temporal para mostrar la foto
            vistaFoto.hidden = false;
            guiaFoto.hidden = true;
        } else {
            marcarError("imagen", true);
            entradaFoto.value = "";
        }
    });

    // Contador de caracteres de la descripción: "35 / 280"
    function actualizarContador() {
        contador.textContent = descripcion.value.length + " / 280";
    }
    descripcion.addEventListener("input", actualizarContador);
    actualizarContador();

    // Validación antes de enviar
    formProducto.addEventListener("submit", function (evento) {
        let hayErrores = false;

        const nombre = document.getElementById("nombre").value.trim();
        const categoria = document.getElementById("categoria").value;
        const precio = Number(document.getElementById("precio").value);
        const stock = document.getElementById("stock").value;
        const textoDescripcion = descripcion.value.trim();

        // Foto: se escogió una nueva o ya había una (al editar)
        if (entradaFoto.files.length > 0 || vistaFoto.hidden === false) {
            marcarError("imagen", false);
        } else {
            marcarError("imagen", true);
            hayErrores = true;
        }

        if (nombre.length >= 3) {
            marcarError("nombre", false);
        } else {
            marcarError("nombre", true);
            hayErrores = true;
        }

        if (categoria !== "") {
            marcarError("categoria", false);
        } else {
            marcarError("categoria", true);
            hayErrores = true;
        }

        if (precio >= 100) {
            marcarError("precio", false);
        } else {
            marcarError("precio", true);
            hayErrores = true;
        }

        // Stock: un número entero de 0 o más (sin decimales)
        if (stock !== "" && Number(stock) >= 0 && stock.indexOf(".") === -1) {
            marcarError("stock", false);
        } else {
            marcarError("stock", true);
            hayErrores = true;
        }

        if (textoDescripcion.length >= 20) {
            marcarError("descripcion", false);
        } else {
            marcarError("descripcion", true);
            hayErrores = true;
        }

        // preventDefault() detiene el envío del formulario
        if (hayErrores) {
            evento.preventDefault();
        }
    });
}

// ---------- 4. Mi Perfil ----------
const formPerfil = document.getElementById("formPerfil");

if (formPerfil) {
    const entradaLogo = document.getElementById("logo");
    const entradaNombre = document.getElementById("nombre");

    // Vista previa del logo nuevo
    entradaLogo.addEventListener("change", function () {
        const archivo = entradaLogo.files[0];

        if (fotoValida(archivo)) {
            document.getElementById("vistaLogo").src = URL.createObjectURL(archivo);
        } else {
            alert("La foto debe ser JPG, PNG o WEBP de máximo 5 MB.");
            entradaLogo.value = "";
        }
    });

    // El nombre de la tarjeta de arriba cambia mientras se escribe
    entradaNombre.addEventListener("input", function () {
        document.getElementById("nombreVista").textContent = entradaNombre.value;
    });

    formPerfil.addEventListener("submit", function (evento) {
        let hayErrores = false;

        const nombre = entradaNombre.value.trim();
        const descripcion = document.getElementById("descripcion").value.trim();
        const distrito = document.getElementById("distrito").value;
        const telefono = document.getElementById("telefono").value.trim();

        // Cuenta cuántos métodos de pago están marcados
        const pagos = document.getElementsByName("pagos[]");
        let pagosMarcados = 0;
        for (let i = 0; i < pagos.length; i++) {
            if (pagos[i].checked) {
                pagosMarcados++;
            }
        }

        if (nombre.length >= 3) {
            marcarError("nombre", false);
        } else {
            marcarError("nombre", true);
            hayErrores = true;
        }

        if (descripcion.length >= 30) {
            marcarError("descripcion", false);
        } else {
            marcarError("descripcion", true);
            hayErrores = true;
        }

        if (distrito !== "") {
            marcarError("distrito", false);
        } else {
            marcarError("distrito", true);
            hayErrores = true;
        }

        if (telefonoValido(telefono)) {
            marcarError("telefono", false);
        } else {
            marcarError("telefono", true);
            hayErrores = true;
        }

        if (pagosMarcados > 0) {
            marcarError("pagos", false);
        } else {
            marcarError("pagos", true);
            hayErrores = true;
        }

        if (hayErrores) {
            evento.preventDefault();
        }
    });
}
