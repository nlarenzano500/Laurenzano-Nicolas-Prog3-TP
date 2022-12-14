<?php
require_once './models/Operacion.php';

class Logger {

    public static function LogInicial($token) {
        $payload=AutentificadorJWT::ObtenerData($token);

        // Creamos la operación
        $operacion = new Operacion();
        $operacion->usuario = $payload->usuario;
        $operacion->tipo = "login";
        $operacion->crear();
    }

    public static function Log($request, $tipo) {

        $esValido = false;

        try  {
            $token = AutentificadorJWT::ObtenerToken($request);
            AutentificadorJWT::verificarToken($token);

            $payload=AutentificadorJWT::ObtenerData($token);
            if (UsuarioController::VerificarUsuario(array("usuario"=>$payload->usuario, "perfil"=>$payload->perfil, "clave"=>$payload->clave)) )
                $esValido = true;

        } catch (Exception $e) {
            $esValido = false;     
        }

        if($esValido)
            $usuario = $payload->usuario;
        else
            $usuario = "cliente";

        // Creamos la operación
        $operacion = new Operacion();
        $operacion->usuario = $usuario;
        $operacion->tipo = $tipo;
        $operacion->crear();
    }



    public function TraerReporte($request, $response, $args) {
        Logger::Log($request, "consulta operaciones");

        // Tipo de reporte
        $parametros = $request->getParsedBody();
        $tipo = $parametros['tipo'];
        $resultado = null;

        switch ($tipo) {

            case 'login':
                $resultado = Operacion::obtenerLogin();
                break;

            case 'sector':
                $sector = $parametros['sector'];
                $resultado = Operacion::obtenerPorSector($sector);
                break;
            
            case 'sector_emp':
                
                break;
            
            case 'empleado':
                
                break;



            default:
                $resultado = null;
                break;
        }

        if ($resultado)
            $payload = json_encode($resultado);
        else
            $payload = json_encode(array("mensaje" => "No hay resultados."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }




    public function TraerTodos($request, $response, $args)
    {
        $lista = Operacion::obtenerTodos();
        return Logger::ArmarResponseClases($response, $lista, 200);
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

        return Logger::ArmarResponse($response, $mensaje, 200);
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
