<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnexosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexos', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 5);
            $table->integer('user_id')->unsigned();
            $table->integer('estado_id')->unsigned();
            $table->timestamps();
            $table->unique(['name']);
            $table->unique(['name','user_id']);
            
            /*relacion con tabla users*/
            $table->foreign('estado_id')
                  ->references('id') 
                  ->on('estados'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('anexos');
    }
}
