<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'queues';

    public function estrategia(){
        return $this->belongsTo('Cosapi\Models\Queues_Strategies');
    }

    public function prioridad(){
        return $this->belongsTo('Cosapi\Models\Queues_Priorities');
    }
}
