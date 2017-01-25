<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cdr extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'cdr';

    public function scopeFiltro_days($query,$days)
    {

        if( ! empty($days))
        {
            return    $query->whereBetween(DB::raw("DATE(calldate)"),$days);
        }

    }
}
