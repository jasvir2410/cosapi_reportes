<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resultados', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('encuesta_id')->unsigned();
            $table->integer('pregunta_id')->unsigned();
            $table->integer('tipo_encuesta_id')->unsigned();
            $table->string('respuesta', 60)->index('FK_resultados_respuesta');
            $table->date('fecha_encuesta');
            $table->timestamps();
            $table->unique(['encuesta_id','pregunta_id','tipo_encuesta_id','fecha_encuesta'], 'RespuestaUnica_x_Dia');

            /*se crea las relaciones entre tablas y llaves foraneas*/
           /*relacion entre tipo de encuesta y resultado*/
            $table->foreign('tipo_encuesta_id')
                  ->references('id')
                  ->on('tipo_encuestas');

            /*relacion con tabla encuesta*/
            $table->foreign('encuesta_id')
                  ->references('id')
                  ->on('encuestas'); 

            /*relacion con tabla encuesta*/
            $table->foreign('pregunta_id')
                  ->references('id')
                  ->on('preguntas');              
           
                  
        });

    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resultados');
    }
}
