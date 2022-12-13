<?php

class Operacion {

    public $usuario;
    public $fecha;
    public $tipo;

    public function crear() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO operaciones (usuario, tipo) VALUES (:usuario, :tipo)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        try {
            $consulta->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function obtenerLogin() {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT usuario, tipo, fecha FROM operaciones WHERE tipo = :tipo");
        $consulta->bindValue(':tipo', "login", PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Operacion');
    }
    
    public static function obtenerPorSector($sector) {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if ($sector == null) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT op.usuario, op.tipo, op.fecha FROM operaciones op INNER JOIN usuarios u ON op.usuario = u.usuario WHERE u.sector is NULL");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT op.usuario, op.tipo, op.fecha FROM operaciones op INNER JOIN usuarios u ON op.usuario = u.usuario WHERE u.sector = :sector");
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        }

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Operacion');
    }




}
