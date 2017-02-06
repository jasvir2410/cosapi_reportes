<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueStatsMvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_stats_mv', function (Blueprint $table){
                
            $table->increments('id');
            $table->timestamp('DATETIME')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('datetimeconnect')->default('0000-00-00 00:00:00');
            $table->dateTime('datetimeend')->default('0000-00-00 00:00:00');
            $table->string('queue', 100)->default('');
            $table->string('agent', 100)->default('');
            $table->string('event', 40)->default('');
            $table->string('uniqueid', 50)->default('')->unique();
            $table->string('clid', 50)->default('');
            $table->string('url', 100)->default('');
            $table->integer('POSITION')->unsigned()->default(1);
            $table->string('info1', 50)->default('');
            $table->string('info2', 50)->default('');
            $table->integer('overflow')->unsigned()->default(1);
            $table->unique(['uniqueid','queue','event'], 'uni');
            $table->index(['event','DATETIME'], 'filtro_kpi_dashboard');
            $table->index(['clid','agent','queue','info2','event','url','info1','DATETIME'], 'filtro_calls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('queue_stats_mv');
    }
}
