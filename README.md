# Ambiente-Web-Proyecto

# Ventica

**Mercado de emprendimientos costarricenses.**
Ventica es una aplicación web que conecta a emprendedores y artesanos ticos con las personas que quieren comprarles directamente, sin intermediarios y con pago por SINPE Móvil.

Proyecto Integrador del curso **SC-502 Ambiente Web Cliente/Servidor** — Universidad Fidélitas.

---

## Equipo

| Integrante | Rama de trabajo |
|---|---|
| David Céspedes Camacho | `feature/david-cliente-vendedor` |
| Ronald Calderón | `feature/ronald-vendedor-admin` |
| Sofía Loaiza Cabalceta | `feature/sofia-cliente-base` |
| Susan Echavarría Céspedes | `feature/susan-admin` |

---

## Tecnologías

- **HTML5** y **CSS3** (estilos propios basados en el diseño de Figma, sin frameworks)
- **JavaScript** (validaciones e interacciones en el navegador)
- **PHP 8** con patrón **MVC** (Modelo – Vista – Controlador)
- **MySQL 8** en la nube (Aiven) con procedimientos almacenados
- **Render** para publicar la aplicación (Docker)

Diseño: [Figma de Ventica](https://www.figma.com/design/i4LxkTnhAUjr3EwJdmd8TK/Ventica)

---

## Estructura del proyecto

```
VenticaProyecto/
├── app/
│   ├── core/           → Router, Controller y Repository base, conexión (Database.php)
│   ├── controllers/    → Reciben la petición y deciden qué vista mostrar
│   ├── services/       → Reglas del negocio (registro, carrito, pagos, reportes…)
│   ├── repositories/   → Solo ejecutan procedimientos almacenados (CALL sp_…)
│   ├── models/         → Clases de cada tabla (Usuario, Producto, Pedido…)
│   ├── helpers/        → Vista.php, Sesion.php, Validador.php
│   └── views/          → Pantallas en PHP, una carpeta por módulo
│       ├── general/    → header, navbar, footer, mensajes, plantilla, historias, ayuda…
│       └── correo/     → Plantillas de los correos
├── config/             → config.php, rutas.php, database.php, seguridad.php, correo.php
├── database/
│   ├── 01_tablas.sql           → Crea la base "ventica": 24 tablas FIDE_..._TB, vistas y provincias/cantones/distritos
│   ├── 02_procedimientos.sql   → Procedimientos almacenados (sp_tabla_accion)
│   ├── 03_datos_prueba.sql     → Datos para probar con los 3 roles
│   └── instalar.php            → Corre los 3 scripts en orden
├── docs/               → Estándares del equipo
├── public/             → Lo único visible desde el navegador
│   ├── index.php       → Punto de entrada: carga la configuración y el Router
│   └── static/         → css, js, img (iconos.svg y fotos) y favicon
└── Dockerfile          → Para publicar en Render
```

Cómo viaja una petición: `public/index.php` → `Router` (busca la URL en `config/rutas.php`) → **Controller** → **Service** (valida y calcula) → **Repository** (`CALL sp_…`) → base de datos. El controlador termina cargando la **vista** con los datos.

---

## Cómo correr el proyecto

### 1. Requisitos
- [XAMPP](https://www.apachefriends.org/) con PHP 8 (solo se usa su PHP; la base está en Aiven).
- Git y Visual Studio Code.

### 2. Clonar el repositorio
```bash
git clone https://github.com/davalej08/Ambiente-Web-Proyecto.git
cd Ambiente-Web-Proyecto
```

### 3. Conectarse a la base de datos compartida (Aiven)
El grupo usa una sola base MySQL en la nube (Aiven), así todos ven los mismos datos.

1. Copiar `config/db.example.php` como `config/db.php` y llenar host, puerto y contraseña (se comparten por privado, **nunca en GitHub**).
2. El certificado `config/ca.pem` ya viene en el repositorio (no es secreto).
3. Para crear o reiniciar las tablas (solo una persona, avisando al grupo): `php database/instalar.php`. Ejecuta en orden `01_tablas.sql`, `02_procedimientos.sql` y `03_datos_prueba.sql`. **Borra los datos que hubiera.**
4. Probar la conexión en `http://localhost:8000/prueba_conexion.php`.

### 4. Levantar la aplicación
```powershell
# Windows (PowerShell) con XAMPP, parados en la carpeta del proyecto
& "C:\xampp\php\php.exe" -S localhost:8000 -t public public/index.php
```
Y abrir http://localhost:8000

### 5. Cuentas de prueba
Todas usan la contraseña **Ventica2026**. Para registrar cuentas nuevas se pide cédula, nombre y apellidos; las direcciones se eligen por provincia, cantón y distrito.

| Rol | Correo | Qué se puede probar |
|---|---|---|
| Cliente | cliente@ventica.cr | Comprar, pagar por SINPE, Mi perfil, pedidos y favoritos |
| Emprendedor | emprendedor@ventica.cr | Portal: productos, pedidos, reportes y perfil de la vitrina |
| Administrador | admin@ventica.cr | Aprobar solicitudes, suspender, usuarios, categorías y reportes |

Los correos (bienvenida, recuperar contraseña, respuesta a solicitudes) están en **modo de prueba**: no se envían, se guardan en `storage/correos/` y el enlace de "Recuperar contraseña" se muestra en pantalla. Para enviarlos de verdad hay que configurar `config/correo.php`.

### 6. Pantallas

| Pantalla | URL | Acceso |
|---|---|---|
| Landing | / | Público |
| Catálogo con filtros | /catalogo | Público |
| Vitrina del emprendimiento | /producto/detalle?id=1 | Público |
| Historias de Artesanos | /historias | Público |
| Preguntas frecuentes, Términos, Contacto | /ayuda, /terminos, /contacto | Público |
| Iniciar sesión, Crear cuenta | /auth/login, /auth/registro | Público |
| Registro de emprendedor | /auth/registro-vendedor | Público |
| Recuperar y cambiar contraseña | /auth/recuperar | Público |
| Carrito | /carrito | Público (para pagar hay que iniciar sesión) |
| Confirmación del pedido | /carrito/confirmacion?id= | Cliente |
| Mi perfil (datos, pedidos, favoritos) | /usuario/perfil | Cliente |
| Portal del emprendedor | /vendedor/dashboard, /vendedor/productos, /vendedor/pedidos, /vendedor/reportes, /vendedor/perfil | Emprendedor |
| Panel del administrador | /admin/dashboard, /admin/solicitudes, /admin/emprendimientos, /admin/usuarios, /admin/categorias, /admin/reportes | Administrador |
| Guía de componentes | /plantilla | Equipo |

Para agregar una pantalla: una línea en `config/rutas.php` y el método en el controlador.

---

## Publicar en Render

1. En [render.com](https://render.com) > **New** > **Web Service** > conectar el repositorio de GitHub (rama `main`).
2. **Language / Runtime:** Docker (usa el `Dockerfile` del proyecto). **Instance type:** Free.
3. En **Environment** agregar las variables (los mismos datos de `config/db.php`):
   `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME` (= ventica).
4. **Create Web Service**. Cuando termine, Render da un enlace tipo `https://ventica.onrender.com`.

Notas del plan gratuito: el servicio se duerme tras 15 minutos sin visitas (la primera carga tarda cerca de un minuto) y las fotos que se suban se borran cuando se reinicia; las fotos de los datos de prueba sí vienen en el repositorio.

---

## Cómo crear una pantalla nueva

1. Copiar `app/views/general/plantilla.php` a la carpeta del módulo (ej. `app/views/carrito/carrito.php`).
2. Cambiar las variables de arriba: `$titulo`, `$estilos`, `$scripts`, `$paginaActiva`.
3. Escribir la pantalla dentro de `<main>`.
4. Los estilos van en el CSS del módulo (ej. `carrito.css`). **No modificar `general.css`**: es compartido por todos.
5. Reutilizar los componentes que ya existen (botones `.btn`, campos `.campo`, tarjetas `.tarjeta`, tablas `.tabla`, estados `.estado`…). Están todos de muestra en `plantilla.php`.

Funciones disponibles en las vistas (`app/helpers/Vista.php`):

| Función | Para qué sirve | Ejemplo |
|---|---|---|
| `e($texto)` | Escapar texto antes de mostrarlo | `<?php echo e($producto['nombre']); ?>` |
| `ruta($ruta)` | Armar un enlace interno | `ruta('catalogo')` |
| `recurso($archivo)` | Enlazar CSS, JS o imágenes | `recurso('img/logo-ventica.png')` |
| `icono($nombre)` | Mostrar un ícono | `icono('carrito')` |
| `colones($monto)` | Formato de precio | `colones(9500)` → ₡9,500 |

---

## Forma de trabajo en Git

- Cada integrante trabaja en **su propia rama** `feature/…` (nunca directo en `main`).
- **Commits pequeños y frecuentes**, con mensajes claros: qué se hizo y en qué pantalla.
  Ejemplo: `Pantalla de carrito con resumen de compra`.
- Cuando una parte está lista y probada, se hace un **Pull Request** hacia `main` y otra persona del equipo lo revisa antes de hacer *merge*.
- Antes de empezar a trabajar, traer los cambios de `main`:
  ```bash
  git pull origin main
  ```

---

## Estado del proyecto

| Módulo | Contenido | Estado |
|---|---|---|
| Base | Estructura MVC, estilos generales, header, navbar, footer, plantilla | Listo |
| Base de datos | Modelo E-R del Avance 1 (24 tablas FIDE_..._TB), división territorial completa, procedimientos y datos de prueba | Listo |
| Backend | Router, controladores, servicios, repositorios, modelos, sesiones y roles | Listo |
| Cliente | Landing, catálogo, vitrina, carrito, confirmación, Mi perfil, favoritos | Listo |
| Acceso | Iniciar sesión, crear cuenta, registro de emprendedor, recuperar contraseña | Listo |
| Páginas | Historias de Artesanos, Preguntas frecuentes, Términos, Contacto, 404 | Listo |
| Vendedor | Dashboard, productos, pedidos, comprobante, reportes, perfil | Listo |
| Administrador | Dashboard, solicitudes, emprendimientos, usuarios, categorías, reportes | Listo |
| Publicación | Dockerfile para Render | Listo |
