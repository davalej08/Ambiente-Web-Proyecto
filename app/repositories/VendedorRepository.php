<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * VendedorRepository
 * Acceso a FIDE_EMPRENDIMIENTO_TB y sus métodos de pago y redes sociales mediante procedimientos almacenados.
 */

class VendedorRepository extends Repository
{
    /**
     * Registra la solicitud del emprendimiento (queda pendiente de aprobación).
     *
     * @param Vendedor $vendedor
     * @return int Id del emprendimiento nuevo
     */
    public function crear($vendedor)
    {
        return $this->insertar('CALL sp_emprendimiento_insertar(?, ?, ?, ?, ?, ?, ?)', [
            $vendedor->getIdUsuario(),
            $vendedor->getNombreNegocio(),
            $vendedor->getDescripcion(),
            $vendedor->getIdDistrito(),
            $vendedor->getOtrasSenas(),
            $vendedor->getLogo(),
            $vendedor->getMetodosPago(),
        ]);
    }

    /**
     * Emprendimiento con dueño, calificación y totales.
     *
     * @param int $idEmprendimiento
     * @return array|null
     */
    public function obtener($idEmprendimiento)
    {
        return $this->consultarUno('CALL sp_emprendimiento_obtener(?)', [$idEmprendimiento]);
    }

    /**
     * @param int $idUsuario
     * @return array|null
     */
    public function obtenerPorUsuario($idUsuario)
    {
        $fila = $this->consultarUno('CALL sp_emprendimiento_obtener_por_usuario(?)', [$idUsuario]);
        if ($fila === null || $fila['id_emprendimiento'] === null) {
            return null;
        }
        return $fila;
    }

    /**
     * Guarda la vitrina. Si el logo viene vacío se conserva el anterior.
     *
     * @param Vendedor $vendedor
     */
    public function actualizar($vendedor)
    {
        $this->ejecutar('CALL sp_emprendimiento_actualizar(?, ?, ?, ?, ?, ?, ?)', [
            $vendedor->getIdEmprendimiento(),
            $vendedor->getNombreNegocio(),
            $vendedor->getDescripcion(),
            $vendedor->getIdDistrito(),
            $vendedor->getOtrasSenas(),
            $vendedor->getLogo(),
            $vendedor->getMetodosPago(),
        ]);
    }

    /**
     * @param string $estado pendiente | aprobado | rechazado
     * @return array
     */
    public function listarPorEstado($estado)
    {
        return $this->consultar('CALL sp_emprendimiento_listar_por_estado(?)', [$estado]);
    }

    /**
     * Aprueba o rechaza una solicitud.
     *
     * @param int $idEmprendimiento
     * @param string $estado aprobado | rechazado
     * @param string $observacion
     */
    public function resolver($idEmprendimiento, $estado, $observacion)
    {
        $this->ejecutar('CALL sp_emprendimiento_resolver(?, ?, ?)', [$idEmprendimiento, $estado, $observacion]);
    }

    /** @return array Emprendimientos aprobados para Historias de Artesanos */
    public function listarHistorias()
    {
        return $this->consultar('CALL sp_emprendimiento_listar_historias()');
    }

    /**
     * @param int $idEmprendimiento
     * @return array Filas ['plataforma', 'url_perfil'] (FIDE_RED_SOCIAL_TB)
     */
    public function listarRedes($idEmprendimiento)
    {
        return $this->consultar('CALL sp_red_social_listar(?)', [$idEmprendimiento]);
    }
}
