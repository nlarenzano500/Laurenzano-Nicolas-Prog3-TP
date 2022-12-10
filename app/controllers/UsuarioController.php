<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController implements IApiUsable {

    public static function VerificarUsuario($datos) {
        // Buscamos usuario
        $usr = $datos['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        
        if ($usuario->perfil == $datos['perfil'] && password_verify($datos['clave'], $usuario->clave) )
            return true;
        else
            return false;
    }

    public function CargarUno($request, $response, $args) {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->nombre = $nombre;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con éxito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args) {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) {

        $parametros = $request->getParsedBody();

        $idUsuario = $parametros['id_usuario'];
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];
        $fecha_baja = $parametros['fecha_baja'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->nombre = $nombre;
        $usr->fecha_baja = $fecha_baja;

        if ($usr->modificarUsuario($idUsuario)) {
            $payload = json_encode(array("mensaje" => "Usuario modificado."));
        } else {
            $payload = json_encode(array("mensaje" => "El usuario no fue modificado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        $parametros = $request->getParsedBody();

        $idUsuario = $parametros['id_usuario'];

        if (Usuario::borrarUsuario($idUsuario)) {
            $payload = json_encode(array("mensaje" => "Usuario eliminado."));
        } else {
            $payload = json_encode(array("mensaje" => "El usuario no fue eliminado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function SuspenderUno($request, $response, $args) {

        $parametros = $request->getParsedBody();

        $idUsuario = $parametros['id_usuario'];

        if (Usuario::suspenderUsuario($idUsuario)) {
            $payload = json_encode(array("mensaje" => "Usuario suspendido."));
        } else {
            $payload = json_encode(array("mensaje" => "El usuario no fue modificado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
