<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleEventosHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_eventos_history', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('evento_id')->unsigned()->index('detalle_eventos_evento_id_foreign');
            $table->integer('user_id')->unsigned()->index('detalle_eventos_user_id_foreign');
            $table->dateTime('fecha_evento');
            $table->string('ip_cliente', 20);
            $table->string('observaciones', 80);
            $table->dateTime('date_really');
            $table->string('anexo', 5);
            /*creando las relaciones entre tablas y llaves foraneas*/

            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('detalle_eventos_history');
    }
}
