<?php
require_once './models/Mesa.php';
// require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa //implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $numero = $parametros['numero'];
        $estado = $parametros['estado'];
        $codigo = $parametros['codigo'];


        // Creamos el usuario
        $mesa = new Mesa();
        $mesa->numero = $numero;
        $mesa->estado = $estado;
        $mesa->codigo = $codigo;

        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}


?>