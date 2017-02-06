<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
     {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('primer_nombre');
            $table->string('segundo_nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('username')->unique();
            $table->integer('agente_id')->unsigned();
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->enum('role',['user','editor','admin']);
            $table->rememberToken();
            $table->timestamps();

            /*creando las relaciones entre tablas y llaves primarias*/
            $table->foreign('agente_id')
                  ->references('id')
                  ->on('agentes');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
