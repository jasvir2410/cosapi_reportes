<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Queue_Strategy extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'queues_strategies';
}
