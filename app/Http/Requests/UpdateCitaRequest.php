<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['sometimes', 'integer', 'exists:pacientes,id'],
            'doctor_id' => ['sometimes', 'integer', 'exists:doctores,id'],
            'fecha' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'hora_inicio' => ['sometimes', 'date_format:H:i'],
            'hora_fin' => ['sometimes', 'date_format:H:i', 'after:hora_inicio'],
            'motivo' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.date_format' => 'La fecha debe tener formato Y-m-d.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato H:i.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'paciente_id.exists' => 'El paciente no existe.',
            'doctor_id.exists' => 'El doctor no existe.',
        ];
    }
}
