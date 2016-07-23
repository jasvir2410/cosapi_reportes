<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
	protected $connection   = 'laravel';
	protected $table  	 	= 'agentes';
	protected $guarded 		= ['id','name'];
}



