<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class QueuePriority extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'queues_prioritys';
}
