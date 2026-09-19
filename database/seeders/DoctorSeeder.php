<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSeeder extends Seeder
{
    /**
     * RQF-01: datos semilla de doctores.
     */
    public function run(): void
    {
        $doctores = [
            ['nombre' => 'Dr. Andrés Vega', 'especialidad' => 'Medicina General', 'email' => 'a.vega@clinica.com', 'telefono' => '555-0201'],
            ['nombre' => 'Dra. Patricia Ríos', 'especialidad' => 'Pediatría', 'email' => 'p.rios@clinica.com', 'telefono' => '555-0202'],
            ['nombre' => 'Dr. Eduardo Salas', 'especialidad' => 'Cardiología', 'email' => 'e.salas@clinica.com', 'telefono' => '555-0203'],
            ['nombre' => 'Dra. Gabriela Núñez', 'especialidad' => 'Dermatología', 'email' => 'g.nunez@clinica.com', 'telefono' => '555-0204'],
            ['nombre' => 'Dr. Roberto Díaz', 'especialidad' => 'Traumatología', 'email' => 'r.diaz@clinica.com', 'telefono' => '555-0205'],
        ];

        foreach ($doctores as $data) {
            DB::table('doctores')->updateOrInsert(['email' => $data['email']], $data);
        }
    }
}
