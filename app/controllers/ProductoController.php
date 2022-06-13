<?php
require_once './models/Producto.php';
// require_once './interfaces/IApiUsable.php';

class ProductoController //implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $precio = $parametros['precio'];
        $tipo = $parametros['tipo'];
        $nombre = $parametros['nombre'];
        $tiempoProducto = $parametros['tiempoProducto'];
        $stock = $parametros['stock'];

        $prod = new Producto();
        $prod->precio = $precio;
        $prod->tipo = $tipo;
        $prod->nombre = $nombre;
        $prod->tiempoProducto = $tiempoProducto;
        $prod->stock = $stock;

        $prod->save();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
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
    
    public function Modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Borrar($request, $response, $args)
    {
        $res = false;
        try {
            $prods = Usuario::all();
            $prod = $prods->find($args['id']);
            $res = $prod->delete();
        } catch (\Throwable $th) {
            $payload = json_encode(array("mensaje" => "Error al borrar producto: ". $th->getMessage()));
        }

        if($res){
            $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
