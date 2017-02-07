<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create ('detalle_eventos', function (Blueprint $table){
            $table->increments('id');
            $table->integer('evento_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->dateTime('fecha_evento');
            $table->string('ip_cliente',20);
            $table->string('observaciones',80);
            $table->dateTime('date_really')->nullable();
            $table->string('anexo',5)->nullable();

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
        schema::drop('detalle_eventos');
    }
}
