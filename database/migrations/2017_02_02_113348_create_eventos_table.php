<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create ('eventos', function (Blueprint $table){
             $table->increments('id');
             $table->string('name',255)->unique();

             $table->integer('estado_call_id')->unsigned();
             $table->integer('estado_visible_id')->unsigned();
             $table->integer('eventos_auxiliares')->unsigned();
             $table->integer('cosapi_eventos')->unsigned();
             $table->integer('claro_eventos')->unsigned();
             $table->integer('estado_id')->unsigned();
             $table->timestamps();

             /*creando las relaciones de las llaves foraneas*/
             $table->foreign('estado_id')
                ->references('id')
                ->on('estados');

              $table->foreign('estado_call_id')
                ->references('id')
                ->on('estados_calls');

              $table->foreign('estado_visible_id')
                ->references('id')
                ->on('estados_visibles');   

        });        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('eventos');
    }
}
