<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/AutentificadorJWT.php';

class PedidoController implements IApiUsable {

    public function CargarUno($request, $response, $args) {
        Logger::Log($request, "carga pedido");

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
            $mensaje = "Pedido creado.";
        } else {
            $mensaje = "El pedido no fue creado.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }


    public function AgregarFoto($request, $response, $args) {
        Logger::Log($request, "mod pedido");

        $parametros = $request->getParsedBody();
        $codigo = $parametros['codigo'];

        // Buscamos pedido por código
        $pedido = Pedido::obtenerPedido($codigo);

         if ($pedido->agregarFoto()) {
            $mensaje = "Foto agregada.";
        } else {
            $mensaje = "La foto no fue agregada.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }

    public function TraerUno($request, $response, $args) {
        Logger::Log($request, "consulta pedido");

        // Buscamos pedido por código
        $codigo = $args['codigo'];
        $pedido = Pedido::obtenerPedido($codigo);

        if ($pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($codigo);
            $pedido->productos = $productosPedido;
        }

        return PedidoController::ArmarResponseClases($response, $pedido, 200);
    }

    public function TraerTodos($request, $response, $args) {
        Logger::Log($request, "consulta pedidos");

        $lista = Pedido::obtenerTodos();

        foreach ($lista as $pedido) {
            $productosPedido = ProductoPedido::obtenerProductosPorPedido($pedido->codigo);
            $pedido->productos = $productosPedido;
        }

        return PedidoController::ArmarResponseClases($response, $lista, 200);
    }

    public function ModificarUno($request, $response, $args) {
        Logger::Log($request, "mod pedido");

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
            $mensaje = "Pedido modificado.";
        } else {
            $mensaje = "El pedido no fue modificado.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }

    public function BorrarUno($request, $response, $args) {
        Logger::Log($request, "elimina pedido");

        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];

        if (Pedido::borrarPedido($codigo)) {
            // Se borran los productos asociados al pedido
            ProductoPedido::borrarProductosPedidos($codigo);
            $mensaje = "Pedido eliminado.";
        } else {
            $mensaje = "El pedido no fue eliminado.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }
    
    // Trae todos los pedidos en el estado indicado por parámetro
    // Cada empleado verá los pedidos que contengan productos de su sector
    public function TraerPorEstado($request, $response, $args) {
        Logger::Log($request, "consulta pedidos");

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

        return PedidoController::ArmarResponseClases($response, $pedidos, 200);
    }

    public function ModificarEstado($request, $response, $args) {
        Logger::Log($request, "mod pedido");

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
                return PedidoController::ArmarResponse($response,
                 "Solo puede cambiar un pedido a los estados 'En preparación' y 'Listo para servir'.", 401);
            }

            if ($estado == 2 && ($tiempo_estimado == null || $tiempo_estimado == 0)) {
                return PedidoController::ArmarResponse($response,
                 "Al poner un pedido en estado 'En preparación', se necesita el tiempo estimado.", 401);
            }
        } else if ($usuario->perfil == "mozo") {
            // Solo pueden cambiar a los estados:
            //  9 - Cancelado
            if ($estado != 9) {
                return PedidoController::ArmarResponse($response,
                "Solo puede cambiar un pedido al estado 'Cancelado'.", 401);
            }
        }

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->codigo = $codigo;
        $pedido->estado = $estado;
        $pedido->tiempo_estimado = $tiempo_estimado;

        if ($pedido->modificarEstado()) {
            $mensaje = "Pedido modificado.";
        } else {
            $mensaje = "El pedido no fue modificado.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }

    public static function ServirPedido($codigo) {
        // Buscamos pedido por código
        $pedido = Pedido::obtenerPedido($codigo);
        $pedido->estado = 4;
        $pedido->CalcularTiempo();

        $pedido->modificarServido();
    }

    public function AgregarTiempo($request, $response, $args) {
        Logger::Log($request, "mod pedido");

        $mensaje = "";
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        // Obtenemos el usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload=AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($payload->usuario);

        if ($usuario->perfil == "mozo") {
            // Este perfil es el único que no puede agregar tiempo a un pedido
            $mensaje = "No tiene permitido agregar tiempo a los pedidos.";
        } else {
            // Buscamos pedido por código
            $pedido = Pedido::obtenerPedido($codigo);

            if ($pedido->estado != 2) {
                // Solo se puede agregar tiempo a un pedido en estado "En preparación"
                $mensaje = "Solo se puede agregar tiempo a un pedido en estado 'En preparación'.";
            }
        }

        if($mensaje != "") {
            return PedidoController::ArmarResponse($response, $mensaje, 401);
        }

        // Se agrega el tiempo indicado al pedido
        $pedido->tiempo_estimado += $tiempo_estimado;
        if ($pedido->modificarTiempo()) {
            $mensaje = "Pedido modificado.";
        } else {
            $mensaje = "El pedido no fue modificado.";
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }

    public function TraerTiempo($request, $response, $args) {
        Logger::Log($request, "consulta pedido");

        // Buscamos pedido por código
        $parametros = $request->getQueryParams();
        $codigo = $parametros['pedido'];
        $pedido = Pedido::obtenerPedido($codigo);

        // Verificamos que la mesa indicada sea correcta
        $id_mesa = $parametros['mesa'];
        if ($pedido->id_mesa == $id_mesa) {
            date_default_timezone_set("America/Argentina/Buenos_Aires");

            $horario_estimado = date_create();
            date_timestamp_set($horario_estimado, strtotime($pedido->creado));
            date_add($horario_estimado,date_interval_create_from_date_string($pedido->tiempo_estimado." minutes"));
            $hora_actual = date_create();

            if ($horario_estimado < $hora_actual) {
                // Tiempo excedido
                $mensaje = "Tiempo de espera excedido.";

            } else {
                $diferencia = date_diff($horario_estimado,$hora_actual);
                $mensaje = "Tiempo de espera restante: " . $diferencia->format("%H:%i");
            }
        } else {
            $mensaje = "El código de mesa ingresado no corresponde al pedido.";
            return PedidoController::ArmarResponse($response, $mensaje, 401);
        }

        return PedidoController::ArmarResponse($response, $mensaje, 200);
    }

    private static function ArmarResponse($response, $mensaje, $status) {
        $newResponse = $response->withStatus($status);
        $payload = json_encode(array("mensaje"=>$mensaje));
        $newResponse->getBody()->write($payload);
        return $newResponse
          ->withHeader('Content-Type', 'application/json');
    }

    private static function ArmarResponseClases($response, $mensaje, $status) {
        $newResponse = $response->withStatus($status);
        $payload = json_encode($mensaje);
        $newResponse->getBody()->write($payload);
        return $newResponse
          ->withHeader('Content-Type', 'application/json');
    }

}
