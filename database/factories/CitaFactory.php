<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'doctor_id' => Doctor::factory(),
            'fecha' => fake()->date(),
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'estado' => 'pendiente',
            'motivo' => fake()->sentence(4),
        ];
    }
}
