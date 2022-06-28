<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Venta extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'ventas';
    public $incrementing = true;

    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';
    const CREATED_AT = 'fechaAlta';

    protected $fillable = [
        'cantProducto','estado','idPedido',
        'idProducto', 'tiempoEstimado',
        'idUsuario', 'horaInicio', 'horaEntrega',
        'fechaModificacion', 'fechaBaja', 'fechaAlta'
        
    ];

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function pedidos()
    {
        return $this->belongsTo(Pedido::class, 'idPedido');
    }



}

?>