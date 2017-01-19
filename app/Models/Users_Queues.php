<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class User_Queue extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'users_queues';

    public function user(){
        return $this->belongsTo('Cosapi\Models\User');
    }

    public function cola(){
        return $this->belongsTo('Cosapi\Models\Queues');
    }
}
