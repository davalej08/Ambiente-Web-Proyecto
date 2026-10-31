<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Rol
 * Tipo de usuario (FIDE_ROL_TB): Cliente, Emprendedor o Administrador.
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Rol
{
    /** @var int Columna id_rol */
    private $idRol = 0;
    /** @var string Columna nombre_rol */
    private $nombreRol = '';

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Rol($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_rol'])) {
            $this->idRol = (int) $fila['id_rol'];
        }
        if (isset($fila['nombre_rol'])) {
            $this->nombreRol = (string) $fila['nombre_rol'];
        }
    }

    /** @return int */
    public function getIdRol()
    {
        return $this->idRol;
    }

    /** @param int $idRol */
    public function setIdRol($idRol)
    {
        $this->idRol = (int) $idRol;
    }

    /** @return string */
    public function getNombreRol()
    {
        return $this->nombreRol;
    }

    /** @param string $nombreRol */
    public function setNombreRol($nombreRol)
    {
        $this->nombreRol = (string) $nombreRol;
    }
}
