<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController implements IApiUsable {

    public function CargarUno($request, $response, $args) {
        $parametros = $request->getParsedBody();
        
        $id_pedido = $parametros['id_pedido'];
        $id_mesa = $parametros['id_mesa'];

        // Buscamos pedido por código
        $pedido = Pedido::obtenerPedido($id_pedido);

        // Verificamos que la mesa indicada sea correcta
        if ($pedido->id_mesa != $id_mesa) {
            return EncuestaController::ArmarResponse($response,
            "El código de mesa ingresado no corresponde al pedido.", 401);
        }

        $puntaje_mesa = $parametros['puntaje_mesa'];
        $puntaje_restaurante = $parametros['puntaje_restaurante'];
        $puntaje_mozo = $parametros['puntaje_mozo'];
        $puntaje_cocinero = $parametros['puntaje_cocinero'];
        $texto = $parametros['texto'];

        // Creamos la encuesta
        $encuesta = new Encuesta();
        $encuesta->id_pedido = $id_pedido;
        $encuesta->id_mesa = $id_mesa;
        $encuesta->puntaje_mesa = $puntaje_mesa;
        $encuesta->puntaje_restaurante = $puntaje_restaurante;
        $encuesta->puntaje_mozo = $puntaje_mozo;
        $encuesta->puntaje_cocinero = $puntaje_cocinero;
        $encuesta->texto = $texto;

        if ($encuesta->crearEncuesta()) {
            $mensaje = "Encuesta creada.";
        } else {
            $mensaje = "La encuesta no fue creada.";
        }

        return EncuestaController::ArmarResponse($response, $mensaje, 200);
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos encuesta por id_pedido
        $id_pedido = $args['id_pedido'];
        $encuesta = Encuesta::obtenerEncuesta($id_pedido);
        return EncuestaController::ArmarResponseClases($response, $encuesta, 200);
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        return EncuestaController::ArmarResponseClases($response, $lista, 200);
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $id_mesa = $parametros['id_mesa'];
        $puntaje_mesa = $parametros['puntaje_mesa'];
        $puntaje_restaurante = $parametros['puntaje_restaurante'];
        $puntaje_mozo = $parametros['puntaje_mozo'];
        $puntaje_cocinero = $parametros['puntaje_cocinero'];
        $texto = $parametros['texto'];

        // Creamos la encuesta
        $encuesta = new Encuesta();
        $encuesta->id_pedido = $id_pedido;
        $encuesta->id_mesa = $id_mesa;
        $encuesta->puntaje_mesa = $puntaje_mesa;
        $encuesta->puntaje_restaurante = $puntaje_restaurante;
        $encuesta->puntaje_mozo = $puntaje_mozo;
        $encuesta->puntaje_cocinero = $puntaje_cocinero;
        $encuesta->texto = $texto;

        if ($encuesta->modificarEncuesta()) {
            $mensaje = "Encuesta modificada.";
        } else {
            $mensaje = "La encuesta no fue modificada.";
        }

        return EncuestaController::ArmarResponse($response, $mensaje, 200);
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        
        if (Encuesta::borrarEncuesta($id_pedido)) {
            $mensaje = "Mesa eliminada.";
        } else {
            $mensaje = "La encuesta no fue eliminada.";
        }

        return EncuestaController::ArmarResponse($response, $mensaje, 200);
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
