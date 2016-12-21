<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    protected $connection   = 'laravel';
    protected $table 		= 'anexos';

    public function user(){
        return $this->belongsTo('Cosapi\Models\User');
    }
}
