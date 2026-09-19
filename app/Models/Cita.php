<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    public const ESTADOS = ['pendiente', 'confirmada', 'cancelada', 'atendida'];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date:Y-m-d',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
