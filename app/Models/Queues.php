<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'queues';

    public function estrategia(){
        return $this->belongsTo('Cosapi\Models\QueueStrategy','queues_strategy_id');
    }

    public function prioridad(){
        return $this->belongsTo('Cosapi\Models\QueuePriority','queues_priority_id');
    }
}
