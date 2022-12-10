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
            $payload = json_encode(array("mensaje" => "Mesa creada."));
        } else {
            $payload = json_encode(array("mensaje" => "La mesa no fue creada."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por código
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;

        if ($mesa->modificarMesa($codigo)) {
            $payload = json_encode(array("mensaje" => "Mesa modificada."));
        } else {
            $payload = json_encode(array("mensaje" => "La mesa no fue modificada."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        
        if (Mesa::borrarMesa($codigo)) {
            $payload = json_encode(array("mensaje" => "Mesa eliminada."));
        } else {
            $payload = json_encode(array("mensaje" => "La mesa no fue eliminada."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
