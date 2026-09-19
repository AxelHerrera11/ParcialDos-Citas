<?php

namespace App\Services;

use App\Exceptions\ConflictoHorarioException;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Support\Collection;

class CitaService
{
    public function create(array $datos): Cita
    {
        $this->verificarDisponibilidad($datos);

        return Cita::create($datos);
    }

    public function update(Cita $cita, array $datos): Cita
    {
        $datos = array_merge([
            'doctor_id' => $cita->doctor_id,
            'fecha' => $cita->fecha->format('Y-m-d'),
            'hora_inicio' => $cita->hora_inicio,
            'hora_fin' => $cita->hora_fin,
        ], $datos);

        $this->verificarDisponibilidad($datos, excluirId: $cita->id);

        $cita->update($datos);

        return $cita->refresh()->load(['paciente', 'doctor']);
    }
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

    /**
     * RQF-03 / RQNF-07: impide la doble reserva del mismo doctor en horarios que se solapan.
     * Las citas canceladas no ocupan agenda; en actualizaciones se excluye la cita misma.
     */
    private function verificarDisponibilidad(array $datos, ?int $excluirId = null): void
    {
        $fecha = $datos['fecha'] ?? null;
        $doctorId = $datos['doctor_id'] ?? null;
        $inicio = $datos['hora_inicio'] ?? null;
        $fin = $datos['hora_fin'] ?? null;

        if ($fecha === null || $doctorId === null || $inicio === null || $fin === null) {
            return;
        }

        $conflicto = Cita::query()
            ->where('doctor_id', $doctorId)
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where('hora_inicio', '<', $fin)
            ->where('hora_fin', '>', $inicio);

        if ($excluirId !== null) {
            $conflicto->where('id', '!=', $excluirId);
        }

        if ($conflicto->exists()) {
            throw ConflictoHorarioException::doctorOcupado($doctorId, $fecha, $inicio, $fin);
        }
    }
}
