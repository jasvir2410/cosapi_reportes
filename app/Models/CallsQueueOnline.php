<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class CallsQueueOnline extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'calls_queue_online';
}
