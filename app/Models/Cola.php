<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Cola extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'colas';

    public function estrategia(){
        return $this->belongsTo('Cosapi\Models\Estrategia');
    }

    public function prioridad(){
        return $this->belongsTo('Cosapi\Models\Prioridad');
    }
}
