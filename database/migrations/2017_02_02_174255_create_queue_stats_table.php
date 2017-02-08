<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_stats', function (Blueprint $table){

            $table->increments('queue_stats_id');
            $table->string('uniqueid',40)->index('ixuni');
            $table->dateTime('DATETIME')->default('0000-00-00 00:00:00')->index('ixdate');
            $table->integer('qname')->unsigned();
            $table->integer('qagent')->unsigned()->index('ixagent');;
            $table->integer('qevent')->unsigned()->index('ixevent');;
            $table->string('info1', 40)->nullable();
            $table->string('info2', 40)->nullable();
            $table->string('info3', 40)->nullable();
            $table->string('info4', 40)->nullable();
            $table->string('info5', 40)->nullable();
            $table->unique(['uniqueid','DATETIME']);
            $table->unique(['uniqueid','DATETIME','qname','qagent','qevent'], 'unico');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('queue_stats');
    }
}
