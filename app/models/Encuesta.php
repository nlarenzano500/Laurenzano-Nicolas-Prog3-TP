<?php

class Encuesta {
    public $id_pedido;
    public $id_mesa;
    public $puntaje_mesa;
    public $puntaje_restaurante;
    public $puntaje_mozo;
    public $puntaje_cocinero;
    public $texto;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (id_pedido, id_mesa, puntaje_mesa, puntaje_restaurante, puntaje_mozo, puntaje_cocinero, texto) VALUES (:id_pedido, :id_mesa, :puntaje_mesa, :puntaje_restaurante, :puntaje_mozo, :puntaje_cocinero, :texto)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':puntaje_mesa', $this->puntaje_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_restaurante', $this->puntaje_restaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_mozo', $this->puntaje_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_cocinero', $this->puntaje_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':texto', $this->texto, PDO::PARAM_STR);
        try {
            $consulta->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, id_mesa, puntaje_mesa, puntaje_restaurante, puntaje_mozo, puntaje_cocinero, texto FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerEncuesta($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, puntaje_mesa, puntaje_restaurante, puntaje_mozo, puntaje_cocinero, texto FROM encuestas WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

    public function modificarEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE encuestas SET id_mesa = :id_mesa, puntaje_mesa = :puntaje_mesa, puntaje_restaurante = :puntaje_restaurante, puntaje_mozo = :puntaje_mozo, puntaje_cocinero = :puntaje_cocinero, texto = :texto WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':puntaje_mesa', $this->puntaje_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_restaurante', $this->puntaje_restaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_mozo', $this->puntaje_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntaje_cocinero', $this->puntaje_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':texto', $this->texto, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarEncuesta($id_pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM encuestas WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }
}