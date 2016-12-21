<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopeFiltro_days($query,$days)
    {

        if( ! empty($days))
        {
            return    $query->whereBetween(DB::raw("DATE(fecha_evento)"),$days);
        }

    }

    public function scopeSelect_fechamod($query)
    {
        return $query->Select(DB::raw("*,DATE(fecha_evento) as fechamod, TIME(fecha_evento) AS timemod,  DATE_FORMAT((DATE_SUB(fecha_evento, INTERVAL ( MINUTE(fecha_evento)%30 )MINUTE)), '%H:%i') AS hourmod " ));
    }

}
