<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * UbicacionRepository
 * Provincias, cantones y distritos de Costa Rica (FIDE_PROVINCIA_TB, FIDE_CANTON_TB,
 * FIDE_DISTRITO_TB) y direcciones (FIDE_DIRECCION_TB).
 */

class UbicacionRepository extends Repository
{
    /** @return array Todos los distritos con su cantón y provincia */
    public function listar()
    {
        return $this->consultar('CALL sp_ubicacion_listar()');
    }

    /**
     * @param int $idDistrito
     * @return array|null ['id_provincia', 'provincia', 'id_canton', 'canton', 'id_distrito', 'distrito']
     */
    public function obtener($idDistrito)
    {
        return $this->consultarUno('CALL sp_ubicacion_obtener(?)', [$idDistrito]);
    }

    /**
     * Crea una dirección (distrito + otras señas).
     *
     * @param int $idDistrito
     * @param string $otrasSenas
     * @return int Id de la dirección nueva
     */
    public function crearDireccion($idDistrito, $otrasSenas)
    {
        return $this->insertar('CALL sp_direccion_insertar(?, ?)', [$idDistrito, $otrasSenas]);
    }
}
