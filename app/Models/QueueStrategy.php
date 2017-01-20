<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class QueueStrategy extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'queues_strategys';
}
