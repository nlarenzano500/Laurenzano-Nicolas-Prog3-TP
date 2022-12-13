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
        Logger::Log($request, "carga usuario");

        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->nombre = $nombre;
        $usr->setSector($sector);
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con éxito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        Logger::Log($request, "consulta usuario");

        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args) {
        Logger::Log($request, "consulta usuarios");

        $lista = Usuario::obtenerTodos();
        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) {
        Logger::Log($request, "mod usuario");

        $parametros = $request->getParsedBody();

        $idUsuario = $parametros['id_usuario'];
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];
        $fecha_baja = $parametros['fecha_baja'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->nombre = $nombre;
        $usr->setSector($sector);
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
        Logger::Log($request, "elimina usuario");

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
        Logger::Log($request, "suspende usuario");

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

    public function ImportarDatos($request, $response, $args) {
        Logger::Log($request, "importa usuarios");

        $listado = array();

        try {
            // Leer archivo
            $nombreArchivo = "datos/usuarios.csv";
            $archivo = fopen($nombreArchivo, "r");
            if ($archivo) {

                // Recorrer filas del archivo
                while (!feof($archivo)) {
                    $usuario = null;
                    $linea = fgets($archivo);
                    $usuario = str_getcsv($linea);

                    // Agregar cada usuario del archivo a un array
                    if ($usuario != null && $usuario[0] != null) {
                        array_push($listado, $usuario);
                    }
                }


            } else {
                throw new Exception("Ha ocurrido un error al intentar recuperar los datos del archivo.", 1);
            }

        } catch (Exception $e) {
            $listado = null;
        } finally {
            if ($archivo)
                fclose($archivo);
        }

        // Obtenemos datos del usuario a partir de los datos del token
        $token = AutentificadorJWT::ObtenerToken($request);
        $payload = AutentificadorJWT::ObtenerData($token);

        // Elimina usuarios existentes, salvo el que realiza la acción -- CUIDADO CON ESTO --
        Usuario::BorrarTodosMenosYo($payload->usuario);
        foreach ($listado as $usuario) {

            if ($usuario[0] != $payload->usuario) {
                // Creamos el usuario
                $usr = new Usuario();
                $usr->usuario = $usuario[0];
                $usr->clave = $usuario[1];
                $usr->perfil = $usuario[2];
                $usr->nombre = $usuario[3];
                $usr->setSector($usuario[4]);
                $usr->crearUsuario();
            }
         } 
        
        $response->getBody()->write(json_encode(array("mensaje" => "Importación de datos finalizada.")));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ExportarDatos($request, $response, $args) {
        Logger::Log($request, "exporta usuarios");

        $retorno = "";

        $listado = Usuario::obtenerTodos();

        if ($listado != null) {

            try {
                $nombreArchivo = "datos/usuarios_".date('Y-m-d_h-i').".csv";
                $archivo = fopen($nombreArchivo, "w");
                if ($archivo) {
                    $alta = "";
                    foreach ($listado as $usuario) {
                        $alta .= Usuario::FormarLineaCsv($usuario);
                    }

                    fwrite($archivo, $alta);
                    $retorno = "Exportación de datos finalizada: ".$nombreArchivo;
                }
            
            } catch (Exception $e) {
                $retorno = $e;
                
            } finally {
                if ($archivo)
                    fclose($archivo);
            }
        }

        $response->getBody()->write(json_encode(array("mensaje" => $retorno)));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
