<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class AgentesOnline extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'agentes_online';
    protected $primaryKey   = 'id';

    public function scopeSelect_fechamod($query)
    {
        
        return $query->Select(DB::raw("DATE_FORMAT(fecha_evento,'%d/%m/%Y') AS date_agent, DATE_FORMAT(fecha_evento,'%H:%i') AS hour_agent, COUNT(*) AS quantity " ));
    }

    public function scopeFiltro_days($query,$days)
    {

        if( ! empty($days))
        {
            return    $query->whereBetween(DB::raw("DATE(fecha_evento)"),$days);
        }

    }
}
