<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paciente_id' => $this->paciente_id,
            'doctor_id' => $this->doctor_id,
            'paciente' => new PacienteResource($this->whenLoaded('paciente')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'fecha' => $this->fecha->format('Y-m-d'),
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'estado' => $this->estado,
            'motivo' => $this->motivo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
