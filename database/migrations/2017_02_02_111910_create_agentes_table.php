<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agentes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',5)->unique();
            $table->integer('estado_id')->unsigned();
            $table->timestamps();
            /*se crea las relaciones entre tablas y llaves foraneas*/
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
        Schema::drop('agentes');
    }
}
