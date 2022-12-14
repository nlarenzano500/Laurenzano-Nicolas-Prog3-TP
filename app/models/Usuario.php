<?php

class Usuario {
    public $id;
    public $usuario;
    public $clave;
    public $perfil;
    public $nombre;
    public $sector;
    public $fecha_baja;

    public function setSector($sector) {
        // Estos perfiles no pertenecen a un sector específico
        if ($this->perfil == "admin" || $this->perfil == "socio" || $this->perfil == "mozo")
            $this->sector = null;
        
        if ($this->perfil == "admin" || $this->perfil == "socio" || $this->perfil == "mozo")
            $this->sector = null;

        $this->sector = ($sector == "") ? null : $sector;
    }

    public function crearUsuario() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, perfil, nombre, sector) VALUES (:usuario, :clave, :perfil, :nombre, :sector)");

        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, perfil, nombre, sector FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
    
    public static function obtenerUsuario($usuario) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, perfil, nombre, sector FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario($idUsuario) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, perfil = :perfil, nombre = :nombre, sector = :sector, fecha_baja = :fecha_baja WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_baja', $this->fecha_baja, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function suspenderUsuario($idUsuario) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja WHERE id = :id");
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_baja', date_format($fecha, 'Y-m-d H:i:s'));

        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarUsuario($idUsuario) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function BorrarTodosMenosYo($usuario) {
        // ALERTA:  Cuidado al llamar a esta función.
        //          Solo se debe usar antes de importar desde un archivo y teniendo un backup
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM usuarios WHERE usuario != :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function FormarLineaCsv($usuario) {
        $linea = $usuario->id;
        $linea .= "," . $usuario->usuario;
        $linea .= "," . $usuario->perfil;
        $linea .= "," . $usuario->nombre;
        $linea .= "," . $usuario->sector;
        $linea .= "," . $usuario->fecha_baja;
        $linea .= PHP_EOL;

        return $linea;      
    }

}
