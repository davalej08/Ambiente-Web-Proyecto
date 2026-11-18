<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Validador
 * Validaciones de formularios del lado del servidor (las mismas reglas de validaciones.js).
 * El JavaScript ayuda al usuario, pero el servidor SIEMPRE vuelve a revisar.
 */

class Validador
{
    /**
     * Texto con largo mínimo y máximo (cuenta tildes y ñ como una letra).
     *
     * @param string $texto
     * @param int $minimo
     * @param int $maximo
     * @return bool
     */
    public static function texto($texto, $minimo, $maximo)
    {
        $largo = mb_strlen(trim($texto));
        return $largo >= $minimo && $largo <= $maximo;
    }

    /** @return bool true si tiene forma de correo (nombre@dominio.com) */
    public static function correo($correo)
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false && strlen($correo) <= 150;
    }

    /**
     * Teléfono de Costa Rica: 8 dígitos, con o sin guion (8888-8888).
     *
     * @param string $telefono
     * @return bool
     */
    public static function telefono($telefono)
    {
        $soloNumeros = str_replace(['-', ' '], '', $telefono);
        return strlen($soloNumeros) === 8 && ctype_digit($soloNumeros);
    }

    /**
     * Deja el teléfono en formato 8888-8888.
     *
     * @param string $telefono
     * @return string
     */
    public static function formatoTelefono($telefono)
    {
        $soloNumeros = str_replace(['-', ' '], '', $telefono);
        if (strlen($soloNumeros) !== 8) {
            return $telefono;
        }
        return substr($soloNumeros, 0, 4) . '-' . substr($soloNumeros, 4);
    }

    /**
     * Contraseña segura: 8 caracteres o más, al menos un número y una mayúscula.
     * Se revisa letra por letra (igual que en validaciones.js).
     *
     * @param string $contrasena
     * @return bool
     */
    public static function contrasena($contrasena)
    {
        if (strlen($contrasena) < 8) {
            return false;
        }

        $tieneNumero = false;
        $tieneMayuscula = false;
        for ($i = 0; $i < strlen($contrasena); $i++) {
            $letra = $contrasena[$i];
            if (ctype_digit($letra)) {
                $tieneNumero = true;
            }
            if (ctype_upper($letra)) {
                $tieneMayuscula = true;
            }
        }

        return $tieneNumero && $tieneMayuscula;
    }

    /** @return bool true si es un número entero mayor o igual al mínimo */
    public static function entero($valor, $minimo)
    {
        return is_numeric($valor) && (string) (int) $valor === (string) $valor && (int) $valor >= $minimo;
    }

    /** @return bool true si es un número (con o sin decimales) mayor o igual al mínimo */
    public static function numero($valor, $minimo)
    {
        return is_numeric($valor) && (float) $valor >= $minimo;
    }

    /** @return bool true si es una de las 7 provincias */
    public static function provincia($provincia)
    {
        return in_array($provincia, PROVINCIAS, true);
    }

    /**
     * Cédula costarricense solo con números: 9 dígitos la física (1-0234-0567 = 102340567)
     * o hasta 12 un DIMEX.
     *
     * @return bool
     */
    public static function cedula($cedula)
    {
        return ctype_digit($cedula) && strlen($cedula) >= 9 && strlen($cedula) <= 12;
    }

    /**
     * Quita guiones y espacios de la cédula: '1-0234-0567' queda '102340567'.
     *
     * @return string
     */
    public static function formatoCedula($cedula)
    {
        return str_replace(['-', ' '], '', trim($cedula));
    }
}
