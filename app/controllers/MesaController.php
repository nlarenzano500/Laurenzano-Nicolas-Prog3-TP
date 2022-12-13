<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->codigo = $codigo;

        if ($mesa->crearMesa()) {
            $mensaje = "Mesa creada.";
        } else {
            $mensaje = "La mesa no fue creada.";
        }

        return MesaController::ArmarResponse($response, $mensaje, 200);
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por código
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        return MesaController::ArmarResponseClases($response, $mesa, 200);
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        return MesaController::ArmarResponseClases($response, $lista, 200);
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $estado = $parametros['estado'];

        // Obtenemos el usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload=AutentificadorJWT::ObtenerData($token);
        $usuario = Usuario::obtenerUsuario($payload->usuario);

        if ($usuario->perfil == "mozo" && $estado != 2 && $estado != 3) {
            // Este perfil solo puede cambiar a estos estados:
            //      2 - Con cliente comiendo
            //      3 - Con cliente pagando
            return MesaController::ArmarResponse($response,
            "Solo puede cambiar una mesa a los estados 'Con cliente comiendo' y 'Con cliente pagando'.", 401);
        }

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;

        if ($mesa->modificarMesa($id_mesa)) {

            if ($estado == 2) {
                // Al pasar a este estado, se debe cambiar el estado del pedido a "4 - Servido"
                $id_pedido = $parametros['id_pedido'];
                PedidoController::ServirPedido($id_pedido);
            }
            $mensaje = "Mesa modificada.";
        } else {
            $mensaje = "La mesa no fue modificada.";
        }

        return MesaController::ArmarResponse($response, $mensaje, 200);
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        
        if (Mesa::borrarMesa($codigo)) {
            $mensaje = "Mesa eliminada.";
        } else {
            $mensaje = "La mesa no fue eliminada.";
        }

        return MesaController::ArmarResponse($response, $mensaje, 200);
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
