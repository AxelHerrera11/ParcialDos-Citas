<?php

namespace App\Exceptions;

use RuntimeException;

class ConflictoHorarioException extends RuntimeException
{
    public static function doctorOcupado(int $doctorId, string $fecha, string $horaInicio, string $horaFin): self
    {
        $nombreDoctor = \App\Models\Doctor::find($doctorId)?->nombre ?? "ID {$doctorId}";

        return new self(
            "El doctor {$nombreDoctor} ya tiene una cita en el horario {$horaInicio} - {$horaFin} de {$fecha}."
        );
    }
}