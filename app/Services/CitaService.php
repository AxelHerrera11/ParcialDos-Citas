<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Support\Collection;

class CitaService
{
    public function list(array $filtros): Collection
    {
        $query = Cita::query()
            ->with(['paciente', 'doctor'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio');

        if (! empty($filtros['doctor_id'])) {
            $query->where('doctor_id', $filtros['doctor_id']);
        }

        if (! empty($filtros['paciente_id'])) {
            $query->where('paciente_id', $filtros['paciente_id']);
        }

        if (! empty($filtros['desde'])) {
            $query->whereDate('fecha', '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $query->whereDate('fecha', '<=', $filtros['hasta']);
        }

        return $query->get();
    }

    public function create(array $datos): Cita
    {
        return Cita::create($datos);
    }

    public function update(Cita $cita, array $datos): Cita
    {
        $cita->update($datos);

        return $cita->refresh()->load(['paciente', 'doctor']);
    }

    public function changeEstado(Cita $cita, string $estado): Cita
    {
        $cita->update(['estado' => $estado]);

        return $cita->refresh()->load(['paciente', 'doctor']);
    }

    public function findOrFail(int $id): Cita
    {
        return Cita::with(['paciente', 'doctor'])->findOrFail($id);
    }

    public function doctores(): Collection
    {
        return Doctor::orderBy('nombre')->get();
    }

    public function pacientes(): Collection
    {
        return Paciente::orderBy('nombre')->get();
    }
}
