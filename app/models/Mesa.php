<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'mesas';
    public $incrementing = true;

    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';
    const CREATED_AT = 'fechaAlta';
    
    protected $fillable = [
        'codigo', 'numero', 'estado',
        'fechaModificacion', 'fechaBaja', 'fechaAlta'
    ];

}

?>