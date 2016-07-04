<?php


namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class AsteriskCDR extends Model
{
    protected $connection   = 'asterisk';
    protected $table        = 'cdr';

}
