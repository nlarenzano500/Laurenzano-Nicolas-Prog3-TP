<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './models/AutentificadorJWT.php';
require_once './middlewares/MWparaCORS.php';
require_once './middlewares/MWparaAutentificar.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
// $app->setBasePath('/app');
$app->setBasePath('/prog3/TP-Laurenzano/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->post('/usuarios/login', function (Request $request, Response $response) {
        $datos = $request->getParsedBody();

        if (UsuarioController::VerificarUsuario($datos) ) {
            $token= AutentificadorJWT::CrearToken($datos); 
            $payload = json_encode($token);
            $newResponse = $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        } else {
            $newResponse = $response->getBody()->write("Datos de usuario inválidos.");
            $newResponse = $response->withStatus(401);
        }

        return $newResponse;
    })->add(\MWparaCORS::class . ':HabilitarCORS80');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->put('[/]', \UsuarioController::class . ':ModificarUno');
    $group->put('/suspender', \UsuarioController::class . ':SuspenderUno');
    $group->delete('[/]', \UsuarioController::class . ':BorrarUno');
  })->add(\MWparaAutentificar::class . ':VerificarUsuario_1')->add(\MWparaCORS::class . ':HabilitarCORS80');

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno');
    $group->put('[/]', \ProductoController::class . ':ModificarUno');
    $group->delete('[/]', \ProductoController::class . ':BorrarUno');
  })->add(\MWparaAutentificar::class . ':VerificarUsuario_1')->add(\MWparaCORS::class . ':HabilitarCORS80');

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{codigo}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno');
    $group->put('[/]', \MesaController::class . ':ModificarUno');
    $group->delete('[/]', \MesaController::class . ':BorrarUno');
  })->add(\MWparaAutentificar::class . ':VerificarUsuario_2')->add(\MWparaCORS::class . ':HabilitarCORS80');

// $app->get('/pedidos/tiempoRestante', \PedidoController::class . ':TraerTiempo')->add(\MWparaAutentificar::class . ':VerificarUsuario_4')->add(\MWparaCORS::class . ':HabilitarCORS80');

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('/estado/{estado}', \PedidoController::class . ':TraerPorEstado');

    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/{codigo}', \PedidoController::class . ':TraerUno');
    $group->post('/foto', \PedidoController::class . ':AgregarFoto');
    $group->post('[/]', \PedidoController::class . ':CargarUno');
    $group->put('[/]', \PedidoController::class . ':ModificarUno');
    $group->delete('[/]', \PedidoController::class . ':BorrarUno');
  })->add(\MWparaAutentificar::class . ':VerificarUsuario_3')->add(\MWparaCORS::class . ':HabilitarCORS80');

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("TP Laurenzano");
    return $response;

});

$app->run();
