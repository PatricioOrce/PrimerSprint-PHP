<?php
class CSV{

    public static function LeerProductosCSV (string $path)
    {
        $archivo = fopen($path,"r");
        $archivoLength = filesize($path);
        $listaRetorno = [];
        //Mientras que el puntero de lectura del archivo no haya llegado al final
        while(feof($archivo) == false)
        {
  
            if ($archivoLength < 2)
                break;

            $lineReaded = fgets($archivo,$archivoLength);
            if (strlen($lineReaded) > 1)
            {
                $CommaSeparatedValues = explode(',', $lineReaded);
                $prodAux = new Producto();
                    
                $prodAux->nombre = $CommaSeparatedValues[0];
                $prodAux->precio = $CommaSeparatedValues[1];
                $prodAux->tiempoProducto = $CommaSeparatedValues[2];
                $prodAux->tipo = $CommaSeparatedValues[3];
                $prodAux->stock = $CommaSeparatedValues[4];

                array_push($listaRetorno, $prodAux);
                $prodAux->save();
            }
        }
        fclose($archivo);
        return $listaRetorno;
    }
}











?>