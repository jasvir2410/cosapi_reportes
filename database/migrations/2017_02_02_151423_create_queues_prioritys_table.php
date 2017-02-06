<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueuesPrioritysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues_prioritys', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',70)->unique();
            $table->string('description',70);
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
        Schema::drop('queues_prioritys');
    }
}
