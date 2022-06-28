<?php
require_once './models/Pedido.php';
require_once './models/Venta.php';
require_once './helper/MyValidation.php';

// require_once './interfaces/IApiUsable.php';

class PedidoController //implements IApiUsable
{ 

    public function CargarUno($request, $response, $args)
    {
        $body = json_decode(file_get_contents("php://input"), true);
        var_dump($body);
        $mozo = $body['idMozo'];
        $mesa = $body['idMesa'];
        $nombre = $body['nombreCliente'];
        $productos = $body['productosPedidos'];

        //Validaciones:   (Ver de utilizar try catch)
        if(MyValidation::MesaExists($mesa) && MyValidation::UserExists($mozo) && MyValidation::ProductExists($productos))
        {
            $ped = new Pedido();
            $ped->idUsuario = $mozo;
            $ped->idMesa = $mesa;
            $ped->nombreCliente = $nombre;
            $ped->tiempoEstimado = $this->calcTiempoPedido($productos);
            $ped->importe = $this->calcImportePedido($productos);
            $ped->estado = 'Pendiente';
            $ped->save();

            $this->GenerarVentas($productos, $ped);

            $mozo = Usuario::all()->find($ped->idUsuario);
            $payload = json_encode(array("mensaje" => "Pedido creado con exito, tu mozo sera: $mozo->nombre"));
        }else{
            $payload = json_encode(array("mensaje" => "Inconveniente al crear pedido."));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GenerarVentas($productos, $pedido){


        for ($i=0; $i <count($productos) ; $i++) { 
            $venta = new Venta();
            $venta->idProducto = $productos[$i]['idProducto'];
            $venta->idUsuario = $this->GetUsuarioDisponible($productos[$i]) != null ? $this->GetUsuarioDisponible($productos[$i]) : $pedido->idUsuario;
            $venta->idPedido = $pedido->id;
            $venta->estado = "Pendiente";
            $venta->cantProducto = $productos[$i]['cantidadProducto'];
            $venta->horaInicio = date('H:i:s');

            $this->restarStock($venta);
            $venta->save();
        }
    }

    private function GetUsuarioDisponible($producto)
    {
        $usuarios = Usuario::all();
        if($producto != null)
        {
            foreach ($usuarios as $user) {
                if(($user->rol == 'bartender' && $producto->tipo == 'bar') ||
                   ($user->rol == 'cocinero' && $producto->tipo == 'cocina') ||
                   ($user->rol == 'cervecero' && $producto->tipo == 'cerveceria'))
                {
                return $user->id;
                }
            }
        }
        return null;
    }

    public function restarStock($venta)
    {
        $productos = Producto::all();

        $productoToModify = $productos->find($venta->idProducto);

        $productoToModify->stock = $productoToModify->stock - $venta->cantProducto;

        $productoToModify->save();
    }
  
    private function calcTiempoPedido($productos)
    {
        $maxTiempo = null;
        if(is_array($productos))
        {
            for ($i=0; $i <count($productos); $i++) { 
                $maxTiempo = $productos[0]->tiempoProducto;
                if($maxTiempo < $productos[$i]->tiempoProducto)
                {
                    $maxTiempo = $productos[$i]->tiempoProducto;
                }
            }
        }
        return $maxTiempo != null ? $maxTiempo : 0; 
    }

    private function calcImportePedido($productos)
    {
        $importe = 0;
        if(is_array($productos))
        {
            for ($i=0; $i <count($productos); $i++) { 
                $importe += $productos[$i]->precio;
            }
        }
        return $importe;
    }

    public function TraerUno($request, $response, $args)
    {
        $prods = Producto::all();
        $prod = $prods->find($args['id']);

        $payload = json_encode($prod);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $prods = Producto::all();
        $payload = json_encode(array("listaUsuario" => $prods));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
