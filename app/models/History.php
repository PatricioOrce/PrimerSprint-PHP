<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class History extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'historial';
    public $incremeting = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';

    public $fillable = [
        'accion','resultado','horaAccion',
        'idUsuario','idProducto','idPedido',
        'idMesa','fechaAlta','fechaBaja'
       
    ]; 

    public function usuarioAccion()
    {
        return $this->hasOne(Usuario::class,'idUsuario');
    }

    public function productoAccion()
    {
        return $this->hasOne(Producto::class,'idProducto');
    }

    public function mesaAccion()
    {
        return $this->hasOne(Mesa::class,'idMesa');
    }

    public function pedidoAccion()
    {
        return $this->hasOne(Pedido::class,'idPedido');
    }

    static function SaveHistory($response)
    {
        $resultadoAccion = json_decode($response->getBody());
            
        //Creo la accion y le asigno los datos (recibidos de la response)
        $registroHistorial = new History();
        $registroHistorial->resultado = $resultadoAccion->resultado;
        $registroHistorial->horaAccion = date("d/m/y h:i");
        $registroHistorial->idUsuario = $resultadoAccion->idUsuario;
        $registroHistorial->idProducto = $resultadoAccion->idProducto;
        $registroHistorial->idMesa = $resultadoAccion->idMesa;
        $registroHistorial->idPedido = $resultadoAccion->idPedido;

        $registroHistorial->save();



    }



}








?>