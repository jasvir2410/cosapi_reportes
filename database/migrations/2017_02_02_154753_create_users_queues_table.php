<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_queues', function(Blueprint $table){
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('queue_id')->unsigned();
                $table->enum('priority', array('1','2','3','4','5','7','8','9','10'))->default('1');
                $table->unique(['user_id','queue_id']);

                /*se crea las relaciones entre tablas y llaves foraneas*/
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users');


                $table->foreign('queue_id')
                      ->references('id')
                      ->on('queues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_queues');
    }
}
