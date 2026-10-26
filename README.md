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
- **MySQL / MariaDB** (XAMPP)

Diseño: [Figma de Ventica](https://www.figma.com/design/i4LxkTnhAUjr3EwJdmd8TK/Ventica)

---

## Estructura del proyecto

```
VenticaProyecto/
├── app/
│   ├── core/           → Router, Controller y conexión a la base de datos
│   ├── controllers/    → Reciben la petición y deciden qué vista mostrar
│   ├── services/       → Reglas del negocio (registro, carrito, pagos…)
│   ├── repositories/   → Consultas a la base de datos
│   ├── models/         → Clases de cada tabla (Usuario, Producto, Pedido…)
│   ├── helpers/        → Funciones de apoyo (Vista.php, Sesion.php, Validador.php)
│   └── views/          → Pantallas en PHP, una carpeta por módulo
│       └── general/    → header, navbar, footer, mensajes y plantilla.php
├── config/             → Configuración (base de datos, correo, seguridad)
├── database/
│   ├── 01_tablas.sql           → Crea la base "ventica" y sus 11 tablas
│   ├── 02_procedimientos.sql
│   └── 03_datos_prueba.sql
├── docs/               → Estándares y documentación del equipo
├── lang/               → Textos en español e inglés
└── public/             → Lo único visible desde el navegador
    ├── index.php       → Punto de entrada de la aplicación
    ├── prueba.php      → Servidor de prueba para ver las vistas (desarrollo)
    └── static/
        ├── css/        → general.css (compartido) + un CSS por módulo
        ├── js/         → general.js (compartido) + un JS por módulo
        ├── img/        → logo, íconos (iconos.svg) e imágenes subidas
        └── fav/        → favicon
```

---

## Cómo correr el proyecto

### 1. Requisitos
- [XAMPP](https://www.apachefriends.org/) con PHP 8 y MySQL.
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
3. Probar la conexión en `http://localhost:8000/prueba_conexion.php`.
4. Para crear o cambiar tablas: conectarse con MySQL Workbench a Aiven y ejecutar en orden `database/01_tablas.sql`, `02_procedimientos.sql` y `03_datos_prueba.sql` (solo una persona, una vez).

La conexión está en `app/core/Database.php` (PDO con SSL). Los repositorios la usan así: `$db = Database::conectar();`

### 4. Ver las pantallas
Mientras el Router está en desarrollo, las vistas se prueban con el servidor de PHP:

```powershell
# Windows (PowerShell) con XAMPP
& "C:\xampp\php\php.exe" -S localhost:8000 -t public public/prueba.php
```

Y abrir en el navegador:

| Pantalla | URL |
|---|---|
| Landing | http://localhost:8000/ |
| Catálogo | http://localhost:8000/catalogo |
| Iniciar sesión | http://localhost:8000/auth/login |
| Crear cuenta | http://localhost:8000/auth/registro |
| Plantilla / guía de componentes | http://localhost:8000/plantilla |
| Detalle (vitrina del emprendimiento) | http://localhost:8000/producto/detalle |
| Carrito | http://localhost:8000/carrito |
| Vendedor · Dashboard | http://localhost:8000/vendedor/dashboard |
| Vendedor · Mis Productos | http://localhost:8000/vendedor/productos |
| Vendedor · Agregar producto | http://localhost:8000/vendedor/productos/nuevo |
| Vendedor · Editar producto | http://localhost:8000/vendedor/productos/editar?id=1 |
| Vendedor · Mi Perfil | http://localhost:8000/vendedor/perfil |

Para agregar una pantalla nueva a la prueba, se agrega un `case` en `public/prueba.php`.

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

## Estado del Avance 2

| Módulo | Pantalla | Estado |
|---|---|---|
| Base | Estructura MVC, estilos generales, header, navbar, footer, plantilla | Listo |
| Base de datos | 11 tablas del modelo entidad-relación (`01_tablas.sql`) | Listo |
| Cliente | Landing | Listo |
| Cliente | Iniciar sesión | Listo |
| Cliente | Crear cuenta | Listo |
| Cliente | Home / Catálogo | Listo |
| Cliente | Detalle de producto, Carrito | Listo |
| Vendedor | Dashboard, Mis Productos, Agregar producto, Perfil | Listo |
| Vendedor | Pedidos, Reportes | Pendiente |
| Administrador | Dashboard, Solicitudes, Emprendimientos, Categorías, Usuarios, Reportes | Pendiente |
