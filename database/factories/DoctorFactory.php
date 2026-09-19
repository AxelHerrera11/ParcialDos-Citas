<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'especialidad' => fake()->randomElement(['Medicina General', 'Pediatría', 'Cardiología', 'Dermatología']),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('555-####'),
        ];
    }
}
