<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEventos extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'detalle_eventos';


    public function evento(){
        return $this->belongsTo('Cosapi\Models\Eventos');
    }

    public function user(){
        return $this->belongsTo('Cosapi\Models\User');
    }


}
