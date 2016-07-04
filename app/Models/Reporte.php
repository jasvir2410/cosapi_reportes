<?php

namespace Cosapi\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
      protected $table = 'agentes';
	  //protected $fillable = ['name', 'description'];
	  protected $guarded = ['id','name'];
}



