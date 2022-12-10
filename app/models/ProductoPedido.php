<?php

class ProductoPedido {

    public $id_pedido;
    public $id_producto;
    public $cantidad;
    public $precio_unidad;

    public function crearProductoPedido() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos_pedidos (id_pedido, id_producto, cantidad, precio_unidad) VALUES (:id_pedido, :id_producto, :cantidad, :precio_unidad)");

        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':precio_unidad', $this->precio_unidad, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerProductosPorPedido($id_pedido) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, id_producto, cantidad, precio_unidad FROM productos_pedidos WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }

    // Se borran todos los productos asociados a un pedido
    public static function borrarProductosPedidos($id_pedido) {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM productos_pedidos WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function obtenerNrosPedidoPorSector($sector) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $query = "SELECT DISTINCT pp.id_pedido FROM productos_pedidos pp INNER JOIN productos_stock ps ON pp.id_producto = ps.id";

        // Solo se agrega la condición si el sector fue especificado
        if ($sector != null)
            $query .= " WHERE ps.sector = :sector";

        $consulta = $objAccesoDatos->prepararConsulta($query);
        if ($sector != null)
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
    
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
