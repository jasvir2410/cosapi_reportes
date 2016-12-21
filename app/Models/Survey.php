<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Survey extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'encuestas';

	public function user(){
        return $this->belongsTo('Cosapi\Models\User');
    }

    public function anexo(){
        return $this->belongsTo('Cosapi\Models\Anexo');
    }

    public function scopeSelect_fechamod($query)
    {
        return $query->Select(DB::raw("*,DATE(created_at) as fechamod, TIME(created_at) AS timemod " ));
    }

    public function scopeFiltro_days($query,$days)
    {

        if( ! empty($days))
        {
            return    $query->whereBetween(DB::raw("DATE(created_at)"),$days);
        }

    }

}
