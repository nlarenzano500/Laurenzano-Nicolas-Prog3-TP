<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

class PedidoController implements IApiUsable {

    public function CargarUno($request, $response, $args) {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $id_mesa = $parametros['id_mesa'];
        $cliente = $parametros['cliente'];
        $productos = $parametros['productos'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigo = $codigo;
        $pedido->id_mesa = $id_mesa;
        $pedido->cliente = $cliente;

        $importeTotal = 0;

        // El parámetro 'productos' del request es un array de arrays.
        // El array principal es la lista de productos pedidos.
        // Los arrays internos son pares id_producto/cantidad.
        $productos = json_decode($productos);

        foreach ($productos as $elemento) {
            $productoPedido = new ProductoPedido();

            $productoPedido->id_pedido = $codigo;
            $productoPedido->id_producto = $elemento->id;
            $productoPedido->cantidad = $elemento->cantidad;

            $productoStock = Producto::obtenerProducto($productoPedido->id_producto);
            // Registro cantidad de ventas en stock
            $productoStock->vendidos += $productoPedido->cantidad;
            $productoPedido->precio_unidad = $productoStock->precio;
            $productoPedido->crearProductoPedido();

            $importeTotal += $productoPedido->precio_unidad * $productoPedido->cantidad;
        }

        $pedido->importe = $importeTotal;

        if ($pedido->crearPedido()) {
            $payload = json_encode(array("mensaje" => "Pedido creado."));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido no fue creado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function AgregarFoto($request, $response, $args) {
        $parametros = $request->getParsedBody();
        $codigo = $parametros['codigo'];

       // Buscamos pedido por código
        $pedido = Pedido::obtenerPedido($codigo);

         if ($pedido->agregarFoto()) {
            $payload = json_encode(array("mensaje" => "Foto agregada."));
        } else {
            $payload = json_encode(array("mensaje" => "La foto no fue agregada."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        // Buscamos pedido por código
        $codigo = $args['codigo'];
        $pedido = Pedido::obtenerPedido($codigo);

        if ($pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($codigo);
            $pedido->productos = $productosPedido;
        }

        $payload = json_encode($pedido);

        $response->getBody()->write($payload);

        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args) {
        $lista = Pedido::obtenerTodos();

        foreach ($lista as $pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($pedido->codigo);
            $pedido->productos = $productosPedido;
        }

        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) {

        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $id_mesa = $parametros['id_mesa'];
        $estado = $parametros['estado'];
        $importe = $parametros['importe'];
        $cliente = $parametros['cliente'];
        $tiempo_estimado = $parametros['tiempo_estimado'];
        $tiempo_excedido = $parametros['tiempo_excedido'];
        $productos = $parametros['productos'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->estado = $estado;
        $pedido->cliente = $cliente;
        $pedido->tiempo_estimado = $tiempo_estimado;
        $pedido->tiempo_excedido = $tiempo_excedido;

        if ($productos != null) {
            // Se borran los productos asociados al pedido, que se volverán a generar
            ProductoPedido::borrarProductosPedidos($codigo);
            $importeTotal = 0;

            // El parámetro 'productos' del request es un array de arrays.
            // El array principal es la lista de productos pedidos.
            // Los arrays internos son pares id_producto/cantidad.
            // $productos = json_decode($productos);

            foreach ($productos as $elemento) {
                $productoPedido = new ProductoPedido();
                $productoPedido->id_pedido = $codigo;
                $productoPedido->id_producto = $elemento["id_producto"];
                $productoPedido->cantidad = $elemento["cantidad"];

                $productoStock = Producto::obtenerProducto($productoPedido->id_producto);
                // Registro cantidad de ventas en stock
                $productoStock->vendidos += $productoPedido->cantidad;
                $productoPedido->precio_unidad = $productoStock->precio;
                $productoPedido->crearProductoPedido();

                $importeTotal += $productoPedido->precio_unidad * $productoPedido->cantidad;
            }

            $pedido->importe = $importeTotal;
        } else {
            // Se modifica el importe, sin importar el valor de los productos asociados
            $pedido->importe = $importe;
        }

        if ($pedido->modificarPedido($codigo)) {
            $payload = json_encode(array("mensaje" => "Pedido modificado."));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido no fue modificado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];

        if (Pedido::borrarPedido($codigo)) {
            // Se borran los productos asociados al pedido
            ProductoPedido::borrarProductosPedidos($codigo);
            $payload = json_encode(array("mensaje" => "Pedido eliminado."));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido no fue eliminado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    // Trae todos los pedidos en estado pendiente
    // Cada empleado verá los pedidos que contengan productos de su sector
    public function TraerPorEstado($request, $response, $args) {

        // Obtenemos el estado buscado
        $estado = $args['estado'];

        // Obtenemos el usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload=AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($payload->usuario);

        $nrosPedido = ProductoPedido::obtenerNrosPedidoPorSector($usuario->sector);
var_dump($nrosPedido);



        // $pedidos = Pedido::obtenerProductosPorEstadoSector($estado, $usuario->sector);

        foreach ($lista as $pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($pedido->codigo);
            $pedido->productos = $productosPedido;
        }

        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');



    }




    public function TraerTiempo($request, $response, $args) {

    }

}
