<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CategoriaController
 * Gestión de categorías del catálogo por parte del administrador (RF-10).
 */

class CategoriaController extends Controller
{
    /** @var CategoriaService */
    private $categorias;

    public function __construct()
    {
        $this->categorias = new CategoriaService();
    }

    /** GET admin/categorias */
    public function listado()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->vista('categoria/listado', [
            'categorias' => $this->categorias->listarParaAdmin(),
            'admin'      => AdminController::datosAdmin(),
        ]);
    }

    /**
     * Muestra el resultado (éxito o error) y vuelve a la lista.
     *
     * @param array $resultado ['error', 'mensaje']
     */
    private function terminar($resultado)
    {
        if ($resultado['error'] !== '') {
            Sesion::mensaje('error', $resultado['error']);
        } else {
            Sesion::mensaje('exito', $resultado['mensaje']);
        }
        $this->redirigir('admin/categorias');
    }

    /** POST admin/categorias/guardar  (nueva o editar) */
    public function guardar()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/categorias');

        $this->terminar($this->categorias->guardar([
            'id_categoria' => $this->entrada('id_categoria', '0'),
            'nombre'       => $this->entrada('nombre'),
            'descripcion'  => $this->entrada('descripcion'),
            'artesanal'    => $this->entrada('artesanal', '1'),
        ]));
    }

    /** POST admin/categorias/eliminar */
    public function eliminar()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/categorias');

        $this->terminar($this->categorias->eliminar((int) $this->entrada('id_categoria', '0')));
    }
}
