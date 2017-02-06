<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsolidatedEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consolidated_events', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->date('date')->index('search_date');
            $table->string('agente');
            $table->integer('login');
            $table->integer('acd');
            $table->integer('break');
            $table->integer('sshh');
            $table->integer('refrigerio');
            $table->integer('feedback');
            $table->integer('capacitacion');
            $table->integer('backoffice');
            $table->integer('inbound');
            $table->integer('outbound');
            $table->integer('acw');
            $table->integer('desconectado');
            $table->integer('logueado');
            $table->integer('auxiliar');
            $table->integer('talk_time');
            $table->integer('saliente_hablado');
            $table->unique(['date','agente']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('consolidated_events');
    }
}
