<?php

namespace Cosapi\Models;

use Cosapi\Models\DetalleEventos;

class DetalleEventosHistory extends DetalleEventos
{
    protected $connection   = 'laravel';
    protected $table 		= 'detalle_eventos_history';
}
