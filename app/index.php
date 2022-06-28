<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);
//--------------------Dependecias----------------------
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;


//--------------------Requires-----------------------
require __DIR__ . '/../vendor/autoload.php';
// require_once './middlewares/Logger.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/AuthJWT.php';
require_once './middlewares/Sesion.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/RequerimientosController.php';
require_once './controllers/CsvController.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

//----------------Config Conexion ORM--------------------
$capsule = new Capsule();
$capsule->addConnection([
  'driver' => 'mysql',
  'host' => $_ENV['MYSQL_HOST'],
  'database' => $_ENV['MYSQL_DB'],
  'username' => $_ENV['MYSQL_USER'],
  'password' => $_ENV['MYSQL_PASS'],
  'charset'   => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

//-----------------------RUTEOS-----------------------

//-----------------------Usuarios-----------------------
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(\Sesion::class . ':LogIn');
  $group->get('/{id}', \UsuarioController::class . ':TraerUno')->add(\Sesion::class . ':LogIn');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->delete('[/{id}]', \UsuarioController::class . ':Borrar')->add(\Sesion::class . ':LogIn');
  $group->put('[/{id}]', \UsuarioController::class . ':Modificar')->add(\Sesion::class . ':LogIn');
});

//-----------------------Mesas-----------------------
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->delete('[/{id}]', \MesaController::class . ':Borrar');
  $group->put('[/{id}]', \UsuarioController::class . ':Modificar');
})->add(\Sesion::class . ':LogIn');

//-----------------------Productos-----------------------
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{id}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->delete('[/{id}]', \ProductoController::class . ':Borrar');
  $group->put('[/{id}]', \ProductoController::class . ':Modificar');
})->add(\Sesion::class . ':LogIn');

//-----------------------Pedidos-----------------------
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{id}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  $group->delete('[/{id}]', \PedidoController::class . ':Borrar');
  $group->put('[/{id}]', \PedidoController::class . ':Modificar');
})->add(\Sesion::class . ':LogIn');

//-----------------------Actividades/Requerimientos------------------
$app->group('/requerimientos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \RequerimientosController::class . ':ListarPendientes');
  $group->post('[/]', \RequerimientosController::class . ':TiempoDemoraPedido');
});
//-----------------------Manejo CSV------------------
$app->group('/csv', function (RouteCollectorProxy $group) {
  $group->get('[/]', \CsvController::class . ':ListarPendientes');
  $group->post('[/]', \CsvController::class . ':SaveProductsToDB');
});


$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/crearToken', function (Request $request, Response $response) {
    //Recibo del POSTMAN los datos
    $parametros = $request->getParsedBody();
    var_dump($parametros);
    $username = $parametros['username'];
    $clave = $parametros['clave'];
    $estaEnDB = true; //FUNCION QUE VALIDE SI ESTA EN DB

    if ($estaEnDB == true) {
      //Preparamos los datos para crear el token (mediante un array asociativo)
      $datos = array('user' => $username, 'clave' => $clave);

      //Luego de crear el token, asigno al payload el token como objeto formateado a .json
      $token = AutentificadorJWT::CrearToken($datos);
      $payload = json_encode(array('jwt' => $token));

      $response->getBody()->write($payload);
      $data = AutentificadorJWT::ObtenerData($token);
      var_dump($data->user);
      return $response->withHeader('Content-Type', 'application/json');
    }
  });
//-------------------------------------------------------------------------------------------------------------
  $group->get('/verificarToken', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverPayLoad', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverDatos', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

});

$app->run();
