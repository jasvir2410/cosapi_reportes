<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuesStrategysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::create('queues_strategys', function(Blueprint $table){
            $table->increments('id');
            $table->string('name',70)->unique();
            $table->integer('estado_id')->unsigned();
            /*creando las relaciones con tablas estados*/
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
        Schema::drop('queues_strategys');
    }
}
