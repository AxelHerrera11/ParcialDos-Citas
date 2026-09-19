<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    /**
     * RQF-01: datos semilla de pacientes.
     */
    public function run(): void
    {
        $pacientes = [
            ['nombre' => 'María López', 'email' => 'maria.lopez@example.com', 'telefono' => '555-0101'],
            ['nombre' => 'Carlos Ramírez', 'email' => 'carlos.ramirez@example.com', 'telefono' => '555-0102'],
            ['nombre' => 'Ana Torres', 'email' => 'ana.torres@example.com', 'telefono' => '555-0103'],
            ['nombre' => 'Luis Herrera', 'email' => 'luis.herrera@example.com', 'telefono' => '555-0104'],
            ['nombre' => 'Sofía Castillo', 'email' => 'sofia.castillo@example.com', 'telefono' => '555-0105'],
            ['nombre' => 'Jorge Mendoza', 'email' => 'jorge.mendoza@example.com', 'telefono' => '555-0106'],
            ['nombre' => 'Lucía Fernández', 'email' => 'lucia.fernandez@example.com', 'telefono' => '555-0107'],
            ['nombre' => 'Diego Aguilar', 'email' => 'diego.aguilar@example.com', 'telefono' => '555-0108'],
        ];

        foreach ($pacientes as $data) {
            DB::table('pacientes')->updateOrInsert(['email' => $data['email']], $data);
        }
    }
}