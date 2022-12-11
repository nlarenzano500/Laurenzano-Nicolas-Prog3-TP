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
    
    // Trae todos los pedidos en el estado indicado por parámetro
    // Cada empleado verá los pedidos que contengan productos de su sector
    public function TraerPorEstado($request, $response, $args) {

        // Obtenemos el estado buscado
        $estado = $args['estado'];

        // Obtenemos el usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload=AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($payload->usuario);

        // Obtenemos los nros de pedido
        // Filtrando por estado del pedido y sector de los productos que contiene
        $pedidos = Pedido::obtenerPedidosPorEstadoYSector($estado, $usuario->sector);

        foreach ($pedidos as $pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($pedido->codigo);
            $pedido->productos = $productosPedido;
        }

        $payload = json_encode($pedidos);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstado($request, $response, $args) {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];
        $tiempo_estimado = ($estado == 2) ? $parametros['tiempo_estimado'] : null;

        // Obtenemos el usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload=AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($payload->usuario);

        if ($usuario->perfil == "bartender" || $usuario->perfil == "cervecero" || $usuario->perfil == "cocinero") {
            // Solo pueden cambiar a los estados:
            //  2 - En preparación
            //  3 - Listo para servir
            if ($estado != 2 && $estado != 3) {
                $payload = json_encode(array("mensaje" => "Solo puede cambiar un pedido a los estados 'En preparación' y 'Listo para servir'."));
                $response->getBody()->write($payload);
                $response->withStatus(401);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }

            if ($estado == 2 && ($tiempo_estimado == null || $tiempo_estimado == 0)) {
                $payload = json_encode(array("mensaje" => "Al poner un pedido en estado 'En preparación', se necesita el tiempo estimado."));
                $response->getBody()->write($payload);
                $response->withStatus(401);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }
        } else if ($usuario->perfil == "mozo") {
            // Solo pueden cambiar a los estados:
            //  9 - Cancelado
            if ($estado != 9) {
                $payload = json_encode(array("mensaje" => "Solo puede cambiar un pedido al estado 'Cancelado'."));
                $response->getBody()->write($payload);
                $response->withStatus(401);
                return $response
                  ->withHeader('Content-Type', 'application/json');
            }
        }

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigo = $codigo;
        $pedido->estado = $estado;
        $pedido->tiempo_estimado = $tiempo_estimado;

        if ($pedido->modificarEstado()) {
            $payload = json_encode(array("mensaje" => "Pedido modificado."));
        } else {
            $payload = json_encode(array("mensaje" => "El pedido no fue modificado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }







    public function TraerTiempo($request, $response, $args) {

    }

}
