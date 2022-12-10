<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'clave@5423';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;
    
    public static function CrearToken($datos) {
        $ahora = time();
        // parámetros del payload
        $payload = array(
        	'iat'=>$ahora,
            // 'exp' => $ahora + (60*60),
            'aud' => self::Aud(),
            'data' => $datos,
            'app'=> "Recu 2do Parcial"
        );
     
        // TODO: Guardar operación

        return JWT::encode($payload, self::$claveSecreta);
    }
    
    public static function ObtenerToken($request) {

        $arrayConToken = $request->getHeader('Authorization');
        if(!$arrayConToken) {
            throw new Exception("Es necesario enviar un token.");
        }

        $token = str_ireplace("bearer ", "", $arrayConToken[0]);
        return $token;
    }

    public static function VerificarToken($token) {
        // las siguientes líneas lanzan una excepción, de no ser correcto o de haberse terminado el tiempo       
        if( empty($token) || $token == "") {
            throw new Exception("El token esta vacío.");
        }

        try {
            $decodificado = JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
            );
        } catch (ExpiredException $e) {
            //var_dump($e);
           throw new Exception("Clave fuera de tiempo");
        }
        
        // si no da error,  verifico los datos de AUD que uso para saber de que lugar viene  
        if($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario válido");
        }
    }
   
    public static function ObtenerPayLoad($token) {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token) {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud() {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}