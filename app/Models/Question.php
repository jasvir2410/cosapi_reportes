<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $connection   = 'laravel';
    protected $table        = 'preguntas';

    public function tipo_encuestas(){
        return $this->belongsTo('Cosapi\Models\TipoEncuesta');
    }
}
