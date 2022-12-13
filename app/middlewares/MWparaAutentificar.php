<?php
use Slim\Psr7\Response;
require_once "./models/AutentificadorJWT.php";
require_once "./controllers/UsuarioController.php";

class MWparaAutentificar {

	// Solo usuarios registrados
	// "GET" solo para socios y admin
	// "POST", "PUT" y "DELETE" solo para los admin
	public function VerificarUsuario_1($request, $handler) {
         
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta = "";
		$objDelaRespuesta->esValido = false;

		try  {
			$token = AutentificadorJWT::ObtenerToken($request);
			AutentificadorJWT::verificarToken($token);

			$payload=AutentificadorJWT::ObtenerData($token);
			if (UsuarioController::VerificarUsuario(array("usuario"=>$payload->usuario, "perfil"=>$payload->perfil, "clave"=>$payload->clave)) )
				$objDelaRespuesta->esValido=true;
			else
				$objDelaRespuesta->respuesta="Datos de usuario inválidos.";

		} catch (Exception $e) {
			//guardar en un log
			$objDelaRespuesta->excepcion=$e->getMessage();
			$objDelaRespuesta->esValido=false;     
		}

		if($objDelaRespuesta->esValido) {
			$method = $request->getMethod();
	
			if($method == "GET") {
				// "GET" solo sirve para socios y admin
				if($payload->perfil=="socio" || $payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo socios y administradores.";
				}

			} else {
				// "POST", "PUT" y "DELETE" solo sirven para los admin
				if($payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo administradores.";
				}
			}

		} else {
			$objDelaRespuesta->respuesta="Solo usuarios registrados.";
		}  

		if($objDelaRespuesta->respuesta != "") {
			return MWparaAutentificar::ArmarResponse(new Response(), $objDelaRespuesta->respuesta, 401);
		}
		  
		return $response;
	}

	// Solo usuarios registrados
	// "GET" y "PUT solo para mozos, socios y admin
	// "POST" y "DELETE" solo para los admin
	public function VerificarUsuario_2($request, $handler) {
         
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta = "";
		$objDelaRespuesta->esValido = false;

		try  {
			$token = AutentificadorJWT::ObtenerToken($request);
			AutentificadorJWT::verificarToken($token);

			$payload=AutentificadorJWT::ObtenerData($token);
			if (UsuarioController::VerificarUsuario(array("usuario"=>$payload->usuario, "perfil"=>$payload->perfil, "clave"=>$payload->clave)) )
				$objDelaRespuesta->esValido=true;
			else
				$objDelaRespuesta->respuesta="Datos de usuario inválidos.";

		} catch (Exception $e) {
			//guardar en un log
			$objDelaRespuesta->excepcion=$e->getMessage();
			$objDelaRespuesta->esValido=false;     
		}

		if($objDelaRespuesta->esValido) {
			$method = $request->getMethod();
	
			if($method == "GET" || $method == "PUT") {
				// "GET" y "PUT solo para mozos, socios y admin
				if($payload->perfil=="mozo" || $payload->perfil=="socio" || $payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo mozos, socios y administradores.";
				}

			} else {
				// "POST" y "DELETE" solo para los admin
				if($payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo administradores.";
				}
			}

		} else {
			$objDelaRespuesta->respuesta="Solo usuarios registrados.";
		}  

		if($objDelaRespuesta->respuesta != "") {
			return MWparaAutentificar::ArmarResponse(new Response(), $objDelaRespuesta->respuesta, 401);
		}
		  
		return $response;   
	}

	// Solo usuarios registrados
	// "GET" y "PUT" solo para usuarios registrados
	// "POST" solo para mozos, socios y admin
	// "DELETE" solo para admin
	public function VerificarUsuario_3($request, $handler) {

		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta = "";
		$objDelaRespuesta->esValido = false;

		try  {
			$token = AutentificadorJWT::ObtenerToken($request);
			AutentificadorJWT::verificarToken($token);

			$payload=AutentificadorJWT::ObtenerData($token);
			if (UsuarioController::VerificarUsuario(array("usuario"=>$payload->usuario, "perfil"=>$payload->perfil, "clave"=>$payload->clave)) )
				$objDelaRespuesta->esValido=true;
			else
				$objDelaRespuesta->respuesta="Datos de usuario inválidos.";

		} catch (Exception $e) {
			//guardar en un log
			$objDelaRespuesta->excepcion=$e->getMessage();
			$objDelaRespuesta->esValido=false;     
		}

		if($objDelaRespuesta->esValido) {
			$method = $request->getMethod();
	
			if($method == "GET" || $method == "PUT") {
				// "GET" y "PUT solo para usuarios registrados
				$response = $handler->handle($request);

			} else if($method == "POST") {
				// "POST" solo para mozos, socios y admin
				if($payload->perfil=="mozo" || $payload->perfil=="socio" || $payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo mozos, socios y administradores.";
				}

			} else if($method == "DELETE") {
				// "DELETE" solo para admin
				if($payload->perfil=="admin") {
					$response = $handler->handle($request);

				} else {
					$objDelaRespuesta->respuesta="Solo administradores.";
				}
			}

		} else {
			$objDelaRespuesta->respuesta="Solo usuarios registrados.";
		}  

		if($objDelaRespuesta->respuesta != "") {
			return MWparaAutentificar::ArmarResponse(new Response(), $objDelaRespuesta->respuesta, 401);
		}
		  
		return $response;   
	}

	// "GET" es el único método permitido
	public function VerificarUsuario_4($request, $handler) {

		if($request->getMethod() == "GET") {
			return $handler->handle($request);

		} else {
			// "POST", "PUT" y "DELETE" no están permitidos
			return MWparaAutentificar::ArmarResponse(new Response(), "Consulta no permitida.", 401);
		}
	}

	private static function ArmarResponse($response, $mensaje, $status) {
        $newResponse = $response->withStatus($status);
        $payload = json_encode(array("mensaje"=>$mensaje));
        $newResponse->getBody()->write($payload);
        return $newResponse
          ->withHeader('Content-Type', 'application/json');
    }
}
