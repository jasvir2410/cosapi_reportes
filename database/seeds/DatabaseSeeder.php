<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(EstadosTableSeeder::class);
        $this->call(EstadosVisiblesTableSeeder::class);
        $this->call(EstadosCallsTableSeeder::class);
        $this->call(EventosTableSeeder::class);
        $this->call(AnexosTableSeeder::class);
        $this->call(AgentesTableSeeder::class);
        $this->call(Queues_prioritysTableSeeder::class);
        $this->call(Queues_strategysTableSeeder::class);
        $this->call(QueuesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(Tipo_EncuestasTableSeeder::class);


        Model::reguard();
    }
}
