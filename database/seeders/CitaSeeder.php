<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaSeeder extends Seeder
{
    /**
     * RQF-01: datos semilla de citas distribuidas alrededor de la semana actual.
     */
    public function run(): void
    {
        $pacientes = DB::table('pacientes')->pluck('id')->all();
        $doctores = DB::table('doctores')->pluck('id')->all();

        $hoy = Carbon::today();

        $datos = [
            // [dias_desde_hoy, doctor_idx, paciente_idx, inicio, fin, estado, motivo]
            [-2, 0, 0, '08:00', '09:00', 'atendida', 'Control general'],
            [-2, 1, 1, '09:00', '10:00', 'atendida', 'Vacunación'],
            [-1, 2, 2, '10:00', '11:00', 'confirmada', 'Chequeo cardíaco'],
            [-1, 3, 3, '14:00', '15:00', 'cancelada', 'Consulta dermatológica'],
            [0, 4, 4, '08:00', '09:00', 'pendiente', 'Dolor lumbar'],
            [0, 0, 5, '09:00', '10:00', 'confirmada', 'Chequeo general'],
            [0, 1, 6, '11:00', '12:00', 'pendiente', 'Control pediátrico'],
            [0, 2, 7, '15:00', '16:00', 'confirmada', 'Electrocardiograma'],
            [1, 3, 0, '08:00', '09:00', 'pendiente', 'Revisión de piel'],
            [1, 4, 1, '10:00', '11:00', 'confirmada', 'Fractura de muñeca'],
            [1, 0, 2, '14:00', '15:00', 'pendiente', 'Consulta de medicina general'],
            [2, 1, 3, '08:00', '09:00', 'pendiente', 'Niño con fiebre'],
            [2, 2, 4, '09:00', '10:00', 'confirmada', 'Control de presión'],
            [2, 3, 5, '16:00', '17:00', 'pendiente', 'Estudio de manchas'],
            [3, 0, 6, '10:00', '11:00', 'pendiente', 'Control de rutina'],
            [3, 4, 7, '14:00', '15:00', 'confirmada', 'Terapia de rodilla'],
        ];

        foreach ($datos as [$dias, $doctorPos, $pacientePos, $inicio, $fin, $estado, $motivo]) {
            DB::table('citas')->updateOrInsert(
                [
                    'doctor_id' => $doctores[$doctorPos],
                    'fecha' => $hoy->copy()->addDays($dias)->toDateString(),
                    'hora_inicio' => $inicio,
                ],
                [
                    'paciente_id' => $pacientes[$pacientePos],
                    'hora_fin' => $fin,
                    'estado' => $estado,
                    'motivo' => $motivo,
                ]
            );
        }
    }
}