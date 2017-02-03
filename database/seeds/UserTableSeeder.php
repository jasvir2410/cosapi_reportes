<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'primer_nombre'     =>  'Cosapitest',
            'segundo_nombre'    =>  'Cosapitest',
            'apellido_paterno'  =>  'Cosapitest',
            'apellido_materno'  =>  'Cosapitest',
            'username'          =>  'cosapitest',
            'agente_id'         =>  '1',
            'email'             =>  'rdominguez@cosapidata.com.pe',
            'password'          =>  bcrypt('C0s4pi+est+'),
            'role'              =>  'analista'
        ]);
    }
}
