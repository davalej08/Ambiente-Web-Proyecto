<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * UsuarioRepository
 * Acceso a las tablas usuario y rol mediante procedimientos almacenados.
 */

class UsuarioRepository extends Repository
{
    /**
     * Usuario por correo, con su rol y su emprendimiento (si tiene).
     *
     * @param string $correo
     * @return array|null
     */
    public function obtenerPorCorreo($correo)
    {
        return $this->consultarUno('CALL sp_usuario_obtener_por_correo(?)', [$correo]);
    }

    /**
     * @param int $idUsuario
     * @return array|null
     */
    public function obtenerPorId($idUsuario)
    {
        return $this->consultarUno('CALL sp_usuario_obtener(?)', [$idUsuario]);
    }

    /**
     * Busca un rol por nombre (Cliente, Emprendedor, Administrador).
     *
     * @param string $nombre
     * @return Rol|null
     */
    public function obtenerRol($nombre)
    {
        $fila = $this->consultarUno('CALL sp_rol_obtener_por_nombre(?)', [$nombre]);
        if ($fila === null) {
            return null;
        }
        return new Rol($fila);
    }

    /**
     * Crea el usuario. La contraseña ya debe venir cifrada.
     *
     * @param Usuario $usuario
     * @return int Id del usuario nuevo
     */
    public function crear($usuario)
    {
        return $this->insertar('CALL sp_usuario_insertar(?, ?, ?, ?, ?)', [
            $usuario->getIdRol(),
            $usuario->getNombre(),
            $usuario->getCorreo(),
            $usuario->getContrasena(),
            $usuario->getTelefono(),
        ]);
    }

    /**
     * Guarda nombre, correo, teléfono y dirección predeterminada.
     *
     * @param Usuario $usuario
     */
    public function actualizarPerfil($usuario)
    {
        $this->ejecutar('CALL sp_usuario_actualizar_perfil(?, ?, ?, ?, ?, ?)', [
            $usuario->getIdUsuario(),
            $usuario->getNombre(),
            $usuario->getCorreo(),
            $usuario->getTelefono(),
            $usuario->getProvincia(),
            $usuario->getDireccion(),
        ]);
    }

    /**
     * @param int $idUsuario
     * @param string $contrasenaCifrada Resultado de password_hash()
     */
    public function actualizarContrasena($idUsuario, $contrasenaCifrada)
    {
        $this->ejecutar('CALL sp_usuario_actualizar_contrasena(?, ?)', [$idUsuario, $contrasenaCifrada]);
    }

    /**
     * @param int $idUsuario
     * @param string $estado activo | inactivo | bloqueado
     */
    public function cambiarEstado($idUsuario, $estado)
    {
        $this->ejecutar('CALL sp_usuario_cambiar_estado(?, ?)', [$idUsuario, $estado]);
    }

    /**
     * Cambios que hace el administrador (nombre y correo).
     *
     * @param int $idUsuario
     * @param string $nombre
     * @param string $correo
     */
    public function actualizarDesdeAdmin($idUsuario, $nombre, $correo)
    {
        $this->ejecutar('CALL sp_usuario_actualizar_admin(?, ?, ?)', [$idUsuario, $nombre, $correo]);
    }

    /** @return array Clientes y emprendedores (Gestión de Usuarios) */
    public function listarParaAdmin()
    {
        return $this->consultar('CALL sp_usuario_listar_admin()');
    }

    /** @return array ['todos', 'clientes', 'emprendedores', 'suspendidos'] */
    public function contar()
    {
        return $this->consultarUno('CALL sp_usuario_contar()');
    }
}
