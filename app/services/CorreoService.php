<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CorreoService
 * Envío de correos: bienvenida, recuperación de contraseña y respuesta a solicitudes.
 *
 * El contenido de cada correo está en app/views/correo/.
 * Con CORREO_ACTIVO en false (config/correo.php) el correo no se envía:
 * se guarda como .html en storage/correos/ para poder revisarlo.
 */

class CorreoService
{
    /**
     * Arma el correo con su plantilla y lo envía (o lo guarda en modo de prueba).
     *
     * @param string $para       Correo del destinatario
     * @param string $asunto
     * @param string $plantilla  Archivo de app/views/correo sin .php. Ej: 'bienvenida'
     * @param array $datos       Variables para la plantilla
     * @return bool true si se envió o se guardó
     */
    public function enviar($para, $asunto, $plantilla, $datos)
    {
        $html = $this->armarHtml($plantilla, $datos);

        if (CORREO_ACTIVO) {
            $encabezados = "MIME-Version: 1.0\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n"
                . 'From: ' . CORREO_NOMBRE . ' <' . CORREO_REMITENTE . ">\r\n";
            return mail($para, $asunto, $html, $encabezados);
        }

        // Modo de prueba: se guarda el correo como archivo
        if (!is_dir(CARPETA_CORREOS)) {
            mkdir(CARPETA_CORREOS, 0775, true);
        }
        $nombreArchivo = date('Ymd_His') . '_' . $plantilla . '_' . str_replace(['@', '.'], '_', $para) . '.html';
        return file_put_contents(CARPETA_CORREOS . $nombreArchivo, $html) !== false;
    }

    /** Correo de bienvenida al crear una cuenta. */
    public function enviarBienvenida($correo, $nombre, $esEmprendedor)
    {
        return $this->enviar($correo, 'Bienvenido a Ventica', 'bienvenida', [
            'nombre'        => $nombre,
            'esEmprendedor' => $esEmprendedor,
        ]);
    }

    /** Correo con el enlace para cambiar la contraseña. */
    public function enviarRecuperacion($correo, $nombre, $enlace)
    {
        return $this->enviar($correo, 'Cambiá tu contraseña de Ventica', 'recuperar_contrasena', [
            'nombre' => $nombre,
            'enlace' => $enlace,
        ]);
    }

    /** Correo con la respuesta del administrador a una solicitud de emprendimiento. */
    public function enviarRespuestaSolicitud($correo, $nombre, $negocio, $aprobada, $observacion)
    {
        return $this->enviar($correo, $aprobada ? 'Tu vitrina en Ventica fue aprobada' : 'Revisamos tu solicitud en Ventica', 'solicitud_respuesta', [
            'nombre'      => $nombre,
            'negocio'     => $negocio,
            'aprobada'    => $aprobada,
            'observacion' => $observacion,
        ]);
    }

    /**
     * Carga la plantilla y devuelve el HTML como texto.
     *
     * @param string $plantilla
     * @param array $datos
     * @return string
     */
    private function armarHtml($plantilla, $datos)
    {
        extract($datos);
        // ob_start() guarda lo que imprime la plantilla en vez de mandarlo al navegador
        ob_start();
        require __DIR__ . '/../views/correo/' . $plantilla . '.php';
        return ob_get_clean();
    }
}
