<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once "./models/History.php";

class Sesion{

    public static function LogIn (Request $request, RequestHandler $handler) : ResponseMW {
        $header = $request->getHeaderLine('Authorization');
        if($header != null)
        {
            $token = trim(explode("Bearer", $header)[1]);
            $esValido = false;
            $response = new ResponseMW();
    
            try {
              AutentificadorJWT::verificarToken($token);
              AutentificadorJWT::VerificarUsuario($token);
              $response = $handler->handle($request);
            //   History::SaveHistory($response);
    
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
        }
        return new ResponseMW();
      }
    
}






?>