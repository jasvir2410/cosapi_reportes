<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class 	Queue_Empresa extends Model
{
    protected $connection   = 'queuelog';
    /*protected $table        = 'cc_cdr';
    protected $primaryKey 	= 'id_linea';*/

    protected $table        = 'queue_stats_mv';
    protected $primaryKey   = 'id';



    public function scopeFiltro_id_cdr($query,$value)
    {
        if(	! empty($value))
        {
			return $query->wherein('id_cdr', $value);
        }
    }

    public function scopeFiltro_events($query,$events)
    {

        if( ! empty($events))
        {
            return    $query->whereIn('event', $events);
        }

    }

    public function scopeFiltro_days($query,$days)
    {

        if( ! empty($days))
        {
            return    $query->whereBetween(DB::raw("DATE(datetime)"),$days);
        }

    }

    public function scopeSelect_fechamod($query)
    {
        //return $query->Select(DB::raw('*,DATE(FROM_UNIXTIME(date)) as fechamod, HOUR(FROM_UNIXTIME(DATE)) AS hourmod ' ));
        return $query->Select(DB::raw('*,DATE(datetime) as fechamod, TIME(datetime) AS timemod, HOUR(datetime) AS hourmod ' ));
    }

}
