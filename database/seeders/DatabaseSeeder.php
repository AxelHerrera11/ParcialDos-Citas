<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * RQF-01: datos semilla mínimos para pacientes, doctores y citas.
     */
    public function run(): void
    {
        $this->call([
            PacienteSeeder::class,
            DoctorSeeder::class,
            CitaSeeder::class,
        ]);
    }
}