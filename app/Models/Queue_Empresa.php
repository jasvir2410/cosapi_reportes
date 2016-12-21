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

    /*public function scopeSelect_fechamod($query)
    {
        return $query->Select(DB::raw("*,DATE(datetime) as fechamod, TIME(datetime) AS timemod,  DATE_FORMAT((DATE_SUB(DATETIME, INTERVAL ( MINUTE(DATETIME)%30 )MINUTE)), '%H:%i') AS hourmod " ));
    }*/

    public function scopeSelect_fechamod($query)
    {
        return $query->Select(DB::raw("*,DATE(datetime) as fechamod, TIME(datetime) AS timemod,  DATE_FORMAT((DATE_SUB(DATETIME, INTERVAL ( MINUTE(DATETIME)%30 )MINUTE)), '%H:%i') AS hourmod, DATE_FORMAT((DATE_SUB(DATE_FORMAT(DATE_ADD(DATETIME, INTERVAL info2 SECOND), '%Y-%m-%d %H:%i:%s'),INTERVAL ( MINUTE( DATE_FORMAT(DATE_ADD(DATETIME, INTERVAL info2 SECOND), '%Y-%m-%d %H:%i:%s'))%30) MINUTE)), '%H:%i') AS hour_final " ));
    }

    public function scopeFiltro_users($query,$users)
    {

        if( ! empty($users))
        {
            return    $query->where('agent','like','%'.$users.'%');
        }

    }

    public function scopeFiltro_hours($query,$hours)
    {

        if($hours != '')
        {
            return    $query->where(DB::raw("DATE_FORMAT((DATE_SUB(DATETIME, INTERVAL ( MINUTE(DATETIME)%30 )MINUTE)), '%H:%i')" ),'like',$hours);
        }

    }

    public function scopeFiltro_anexos($query)
    {
        return    $query->where(DB::raw("url"),'!=', 3)
                        ->where('url','not like','2%')
                        ->where('url','not like','9%')
                        ->where('url','not like','1%')
                        ->where('url','not like','0800%')
                        ->where('url','!=','79999');

    }

}
