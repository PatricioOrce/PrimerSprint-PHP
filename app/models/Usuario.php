<?php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'usuarios';
    public $incrementing = true;
    public $timestamps = false;

    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';
    const CREATED_AT = 'fechaAlta';

    protected $fillable = [
        'username', 'clave', 'nombre',
        'apellido', 'edad', 'rol', 'estado',
        'fechaModificacion', 'fechaBaja',
        'fechaAlta'
    ];

}