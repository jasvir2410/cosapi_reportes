<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoEncuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tipo_encuestas', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 60)->unique();
            $table->integer('estado_id')->unsigned();
            $table->timestamps();

            /*fk tipo encuesta*/
            $table->foreign('estado_id', 'FK_tipo_encuestas')->references('id')->on('estados')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tipo_encuestas');
    }
}
