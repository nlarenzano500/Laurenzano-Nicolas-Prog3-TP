<?php

class Pedido {
    public $codigo;
    public $id_mesa;
    public $importe;
    public $estado;
    public $cliente;
    public $foto;
    public $tiempo_estimado;
    public $tiempo_excedido;
    public $creado;
    public $productos;


    public function crearPedido() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, id_mesa, importe, cliente, foto) VALUES (:codigo, :id_mesa, :importe, :cliente, :foto)");

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $this->GuardarImagen();
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function agregarFoto() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET foto = :foto WHERE codigo = :codigo");

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);

        $this->GuardarImagen();
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    private function GuardarImagen() {

        $separador = "_";

        if ($this->foto != null && $this->foto != "") {
            // Se guarda una copia de la foto existente
            try {
                date_default_timezone_set("America/Argentina/Buenos_Aires");
                $time = date_timestamp_get(date_create(date('Y-m-d h:i:sa')));

                $extension = strstr($this->foto, ".", false);
                $nombre_archivo_bkp = "pedido".$separador.$this->codigo.$separador.$time.$extension;

                copy("ImagenesPedidos/".$this->foto, "ImagenesPedidos_Backup/".$nombre_archivo_bkp);
                unlink("ImagenesPedidos/".$this->foto);

            } catch (Exception $e) {
                echo "Ha ocurrido un error al intentar borrar los datos del archivo.";
            }
        }

        $extension = "." . pathinfo($_FILES["foto"]["name"])['extension'];
        $nombre_archivo = "pedido".$separador.$this->codigo.$extension;
        $destino = "ImagenesPedidos/".$nombre_archivo;

        move_uploaded_file($_FILES["foto"]["tmp_name"], $destino);
        $this->foto = $nombre_archivo;
    }

    public static function obtenerTodos() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, id_mesa, importe, estado, cliente, foto, tiempo_estimado, tiempo_excedido, creado FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, id_mesa, importe, estado, cliente, foto, tiempo_estimado, tiempo_excedido, creado FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function modificarPedido($codigo) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET id_mesa = :id_mesa, importe = :importe, estado = :estado, cliente = :cliente, tiempo_estimado = :tiempo_estimado WHERE codigo = :codigo");
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        // TODO: Si hay foto anterior, se guarda una copia
        // $this->GuardarImagen();
        // $consulta->bindValue(':foto', $foto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarPedido($codigo) {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function obtenerPedidosPorEstadoYSector($estado, $sector) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $query = "SELECT * FROM pedidos p WHERE p.estado = :estado";

        // Solo se agrega la condici??n si el sector fue especificado
        if ($sector != null)
            $query .= " AND p.codigo in 
                (SELECT DISTINCT pp.id_pedido FROM productos_pedidos pp 
                INNER JOIN productos_stock ps ON pp.id_producto = ps.id 
                WHERE ps.sector = :sector)";

        $consulta = $objAccesoDatos->prepararConsulta($query);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);

        if ($sector != null)
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
    
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public function modificarEstado() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if ($this->tiempo_estimado == null || $this->tiempo_estimado == 0) {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE codigo = :codigo");

        } else {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE codigo = :codigo");
            $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado, PDO::PARAM_STR);
        }

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function modificarServido() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if ($this->tiempo_excedido == null || $this->tiempo_excedido == 0) {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE codigo = :codigo");

        } else {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado, tiempo_excedido = :tiempo_excedido WHERE codigo = :codigo");
            $consulta->bindValue(':tiempo_excedido', $this->tiempo_excedido, PDO::PARAM_STR);
        }

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function modificarTiempo() {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET tiempo_estimado = :tiempo_estimado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public function CalcularTiempo() {
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $horario_estimado = date_create();
        date_timestamp_set($horario_estimado, strtotime($this->creado));
        date_add($horario_estimado,date_interval_create_from_date_string($this->tiempo_estimado." minutes"));
        $hora_actual = date_create();

        if ($horario_estimado < $hora_actual) {
            // Tiempo excedido
            $diferencia = date_diff($horario_estimado,$hora_actual);
            $this->tiempo_excedido = $diferencia->format("%H:%i");
        }
    }

    public static function NombreEstado($estado) {
        switch ($estado) {
            case 1:
                return "Pendiente";
                break;
            case 2:
                return "En preparacion";
                break;
            case 3:
                return "Listo para servir";
                break;
            case 4:
                return "Servido";
                break;
            case 9:
                return "Cancelado";
                break;
            
            default:
                return "";
                break;
        }
    }
}
    