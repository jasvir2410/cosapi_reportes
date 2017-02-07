<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuestas', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('FK_encuesta_users');
            $table->integer('anexo_id')->unsigned()->index('FK_encuesta_anexo');
            $table->string('telefono_contacto', 15);
            $table->string('opcion_ivr', 35)->nullable();
            $table->integer('duracion')->nullable();
            $table->string('unique_id', 50)->unique();
            $table->integer('tipo_encuesta_id')->unsigned()->index('FK_encuestas_tipo_encuestas');
            $table->timestamps();
            $table->string('evento', 30)->nullable();
            $table->unique(['user_id', 'unique_id']);
            /*creando las relaciones entre tablas y llaves primarias*/
            $table->foreign('tipo_encuesta_id')
                  ->references('id')
                  ->on('tipo_encuestas'); 

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('anexo_id')
                ->references('id')
                ->on('anexos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('encuestas');
    }
}
