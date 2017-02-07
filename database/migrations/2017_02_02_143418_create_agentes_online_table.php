<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentesOnlineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agentes_online', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('evento_id')->unsigned()->index('agents_online_evento_id_foreign');
            $table->integer('user_id')->unsigned()->index('agents_online_user_id_foreign');
            $table->dateTime('fecha_evento');
            $table->unique(['user_id','fecha_evento']);

            /*creando las relaciones entre tablas y llaves primarias con la tabla eventos*/
            $table->foreign('evento_id')
                  ->references('id')
                  ->on('eventos');

            /*creando las relaciones entre tablas y llaves primarias con la tabla eventos*/
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
        Schema::drop('agentes_online');
    }
}
