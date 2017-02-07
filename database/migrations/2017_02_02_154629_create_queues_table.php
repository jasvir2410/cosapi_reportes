<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',70)->unique();
            $table->string('vdn',9)->unique();
            $table->integer('queues_strategy_id')->unsigned();
            $table->integer('queues_priority_id')->unsigned();
            $table->integer('estado_id')->unsigned();
            $table->unique(['name','vdn']);
            /*creando las relacion con tabla estrategia*/
            $table->foreign('queues_strategy_id')
                  ->references('id')
                  ->on('queues_strategys');
            /*creando las relacion con tabla prioridad*/
            $table->foreign('queues_priority_id')
                  ->references('id')
                  ->on('queues_prioritys');
            /*creando las relacion con tabla estados*/
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
        Schema::drop('queues');
    }
}
