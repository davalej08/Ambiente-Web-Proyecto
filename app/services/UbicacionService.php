<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * UbicacionService
 * Datos para los selects de provincia, cantón y distrito (general/ubicacion.php)
 * y validación del distrito que llega de un formulario.
 */

class UbicacionService
{
    /** @var UbicacionRepository */
    private $ubicaciones;

    public function __construct()
    {
        $this->ubicaciones = new UbicacionRepository();
    }

    /**
     * Provincias, cantones y distritos separados, como los usa la vista.
     *
     * @return array ['provincias' => [id => nombre],
     *                'cantones'   => [['id', 'nombre', 'padre']],
     *                'distritos'  => [['id', 'nombre', 'padre']]]
     */
    public function opciones()
    {
        $filas = $this->ubicaciones->listar();
        $provincias = [];
        $cantones = [];
        $distritos = [];
        $cantonesVistos = [];

        foreach ($filas as $fila) {
            $provincias[(int) $fila['id_provincia']] = $fila['provincia'];

            if (!isset($cantonesVistos[$fila['id_canton']])) {
                $cantonesVistos[$fila['id_canton']] = true;
                $cantones[] = ['id' => (int) $fila['id_canton'], 'nombre' => $fila['canton'], 'padre' => (int) $fila['id_provincia']];
            }

            $distritos[] = ['id' => (int) $fila['id_distrito'], 'nombre' => $fila['distrito'], 'padre' => (int) $fila['id_canton']];
        }

        return ['provincias' => $provincias, 'cantones' => $cantones, 'distritos' => $distritos];
    }

    /**
     * Revisa que el distrito exista.
     *
     * @param string $idDistrito Lo que llegó del formulario
     * @return array|null Distrito con su cantón y provincia, o null si no es válido
     */
    public function distrito($idDistrito)
    {
        if (!Validador::entero($idDistrito, 10101)) {
            return null;
        }
        return $this->ubicaciones->obtener((int) $idDistrito);
    }

    /**
     * Ids elegidos para volver a pintar los selects (después de un error o al editar).
     *
     * @param array $datos Fila o formulario con 'provincia', 'canton', 'distrito' o 'id_*'
     * @return array ['provincia' => id, 'canton' => id, 'distrito' => id]
     */
    public function elegidos($datos)
    {
        $elegidos = ['provincia' => 0, 'canton' => 0, 'distrito' => 0];
        $nombres = ['provincia', 'canton', 'distrito'];

        for ($i = 0; $i < count($nombres); $i++) {
            $campo = $nombres[$i];
            if (isset($datos['id_' . $campo]) && $datos['id_' . $campo] !== null) {
                $elegidos[$campo] = (int) $datos['id_' . $campo];
            } elseif (isset($datos[$campo]) && ctype_digit((string) $datos[$campo])) {
                $elegidos[$campo] = (int) $datos[$campo];
            }
        }
        return $elegidos;
    }
}
