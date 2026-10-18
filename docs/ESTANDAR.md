<!--
David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
Ambiente Web Cliente/Servidor · SC-502
Universidad Fidélitas · Costa Rica
-->

# Estándar de programación — Ventica

## 1. Carpetas

- Cada capa tiene su carpeta (`controllers`, `models`, `services`, `repositories`, `views`).
- Las vistas van en una carpeta por módulo dentro de `app/views/`. Lo compartido va en `views/general/`.
- CSS, JS e imágenes van solo en `public/static/`. **No se escribe CSS ni JS dentro de las vistas**: siempre se enlazan con `<link>` y `<script src>`.
- Los scripts SQL van en `database/`, numerados en el orden en que se ejecutan.

## 2. Nombres

| Elemento | Convención | Ejemplo |
|---|---|---|
| Clases | PascalCase, singular | `Producto`, `ProductoService` |
| Controladores | `<Módulo>Controller` | `CarritoController` |
| Servicios | `<Entidad>Service` | `CorreoService` |
| Repositorios | `<Entidad>Repository` | `PedidoRepository` |
| Métodos y variables | camelCase, verbo al inicio en métodos | `obtenerPorId()`, `$totalCarrito` |
| Constantes | MAYÚSCULAS con guion bajo | `DB_HOST` |
| Vistas, CSS, JS | minúscula con guion bajo | `registro_vendedor.php`, `carrito.js` |
| Tablas | minúscula, singular | `producto`, `detalle_pedido` |
| Columnas | minúscula con guion bajo | `id_producto`, `fecha_creacion` |
| Procedimientos almacenados | `sp_<tabla>_<acción>` | `sp_producto_insertar`, `sp_usuario_login` |
| IDs/clases HTML | minúscula con guion | `form-registro`, `btn-agregar-carrito` |

Verbos sugeridos para métodos: `listar`, `obtener`, `crear`, `actualizar`, `desactivar`, `eliminar`, `validar`.

## 3. Comentarios

Todo archivo (PHP, CSS, JS, SQL) empieza con el encabezado del equipo, seguido del nombre y propósito del archivo:

```php
<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ProductoService
 * Lógica de productos y filtros del catálogo.
 */
```

En CSS y JS se usa `/* ... */` y en SQL `-- ...` con el mismo contenido.

Docblock en cada método público:

```php
/**
 * Obtiene los productos activos de una categoría.
 *
 * @param int $idCategoria
 * @return Producto[]
 */
public function listarPorCategoria(int $idCategoria): array
```

En JavaScript y SQL se usa el mismo formato: un comentario de encabezado y un comentario corto antes de cada función o procedimiento.

## 4. Reglas por capa

- **Controller:** recibe datos (`$_GET`, `$_POST`), llama al service y carga la vista. No contiene SQL.
- **Service:** valida, calcula y decide. No contiene SQL.
- **Repository:** solo ejecuta procedimientos almacenados (`CALL sp_...`) con PDO y sentencias preparadas.
- **Model:** solo propiedades, getters y setters.
- **View:** solo muestra datos. Escapar siempre la salida con `htmlspecialchars()`.

## 5. Git

- Rama `main` estable; cada integrante trabaja en una rama `feature/<módulo>`.
- Mensajes de commit en español y en presente: `Agrega filtro por categoría al catálogo`.
- Se hace pull antes de empezar a trabajar y se integra a `main` mediante Pull Request.
