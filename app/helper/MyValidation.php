<?php

class MyValidation{

    public static function MesaExists($mesa) : bool{

        if($mesa != null)
        {
            $mesas = Mesa::all();

            if($mesas->find($mesa) != null)
            {
                return true;
            }
        }
        return false;
    }

    public static function UserExists($user) : bool{

        if($user != null)
        {
            $users = Usuario::all();

            if($users->find($user) != null)
            {
                return true;
            }
        }
        return false;
    }

    public static function ProductExists($product) : bool{

        if($product != null)
        {
            $products = Producto::all();

            if($products->find($product) != null)
            {
                return true;
            }
        }
        return false;
    }

    public static function ProductsExists($productArray) : bool{
        $contador = 0;
        if($productArray != null)
        {
            $products = Producto::all();

            foreach ($productArray as $product) {
                if($products->find($product->id) != null)
                {
                    $contador++;
                }
            }
            $contador == count($productArray) ?  true :  false;
        }
        return false;
    }

    public static function CorrectProduct($product) : bool{
        if(($product->tipo == 'bar' ||
           $product->tipo == 'cocina' ||
           $product->tipo == 'cerveceria' ||
           $product->tipo == 'extra') && (
           $product->precio >= 0 && $product->stock > 0))
        {
            return true;
        }
        return false;
    }

    public static function GenerarListadoPendientes($rol){
        $ventasPendientes = Venta::all()->where('estado', '=', 'pendiente');
        $listado = [];
        foreach ($ventasPendientes as $venta) {
            $user = Usuario::all()->find($venta->idUsuario);
            if($user->rol == $rol){
                array_push($listado, $venta);
            }
        }
        return $listado;
    }
}











?>