<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsQueueOnlineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls_queue_online', function(Blueprint $table)
        {
            $table->string('date_data', 25)->default('sanisidro');
            $table->primary(['date_data']);
            $table->smallInteger('total_calls')->nullable()->default(0);
            $table->smallInteger('total_transfer')->nullable()->default(0);
            $table->smallInteger('total_abandoned')->nullable()->default(0);
            $table->smallInteger('answ_10s')->nullable()->default(0);
            $table->smallInteger('answ_15s')->nullable()->default(0);
            $table->smallInteger('answ_20s')->nullable()->default(0);
            $table->smallInteger('abandon_10s')->nullable()->default(0);
            $table->smallInteger('abandon_15s')->nullable()->default(0);
            $table->smallInteger('abandon_20s')->nullable()->default(0);
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('calls_queue_online');
    }
}
