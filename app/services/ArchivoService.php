<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ArchivoService
 * Subida y validación de imágenes (productos, logos).
 *
 * Las fotos se guardan en public/static/img/uploads/<carpeta>/ y en la base
 * de datos solo se guarda la ruta corta. Ej: productos-nuevos/p12_6531a2.jpg
 */

class ArchivoService
{
    /** Tipos de imagen permitidos => extensión con la que se guardan */
    private $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Revisa si llegó un archivo en el campo indicado.
     *
     * @param string $campo Nombre del input type="file"
     * @return bool
     */
    public function seSubio($campo)
    {
        return isset($_FILES[$campo]) && $_FILES[$campo]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Valida y guarda una imagen subida.
     *
     * @param string $campo    Nombre del input type="file"
     * @param string $carpeta  Carpeta dentro de uploads. Ej: 'productos-nuevos'
     * @param string $prefijo  Inicio del nombre del archivo. Ej: 'p12'
     * @return array ['ok' => bool, 'ruta' => 'carpeta/archivo.jpg', 'error' => 'mensaje']
     */
    public function guardarImagen($campo, $carpeta, $prefijo)
    {
        $archivo = $_FILES[$campo];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'ruta' => '', 'error' => 'No se pudo subir la imagen. Probá con otra.'];
        }

        if ($archivo['size'] > TAMANO_MAXIMO_IMAGEN) {
            return ['ok' => false, 'ruta' => '', 'error' => 'La imagen pesa más de 2 MB.'];
        }

        // getimagesize() lee el archivo de verdad: si no es una imagen devuelve false
        $informacion = getimagesize($archivo['tmp_name']);
        if ($informacion === false || !isset($this->tiposPermitidos[$informacion['mime']])) {
            return ['ok' => false, 'ruta' => '', 'error' => 'La imagen debe ser JPG, PNG o WEBP.'];
        }

        $carpetaDestino = CARPETA_SUBIDAS . $carpeta . '/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0775, true);
        }

        // Nombre único para no reemplazar fotos de otros
        $nombre = $prefijo . '_' . bin2hex(random_bytes(6)) . '.' . $this->tiposPermitidos[$informacion['mime']];

        if (!move_uploaded_file($archivo['tmp_name'], $carpetaDestino . $nombre)) {
            return ['ok' => false, 'ruta' => '', 'error' => 'No se pudo guardar la imagen en el servidor.'];
        }

        return ['ok' => true, 'ruta' => $carpeta . '/' . $nombre, 'error' => ''];
    }
}
