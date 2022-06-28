<?php
require_once './models/Producto.php';
require_once './models/Venta.php';



class RequerimientosController{


    public static function ListarPendientes($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $dataToken = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
        $dataUser = json_decode($dataToken);

        $username = $dataUser->datos->user;
        $user = new Usuario();
        $venta = new Venta();
        // $ventas = $venta->where('username', '=', $username)->get();
        $userEncontrado = $user->where('username', '=', $username)->get();
        try {
            AutentificadorJWT::verificarToken($token);
            $rol = $userEncontrado[0]->rol;
            $payload = json_encode(array('Lista Pendientes' => MyValidation::GenerarListadoPendientes($rol)));

        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function TiempoDemoraPedido($request, $response, $args){
        $parametros = $request->getParsedBody();

    }


}








?>