<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RQF-01 / RQNF-01: esquema de citas con estado y claves foráneas.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'atendida'])->default('pendiente');
            $table->string('motivo');
            $table->timestamps();

            $table->index(['doctor_id', 'fecha'], 'citas_doctor_fecha_idx');
            $table->index(['paciente_id', 'fecha'], 'citas_paciente_fecha_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
