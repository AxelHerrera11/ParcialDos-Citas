<?php

use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PacienteController;
use Illuminate\Support\Facades\Route;

Route::get('citas', [CitaController::class, 'index']);
Route::post('citas', [CitaController::class, 'store']);
Route::get('citas/{cita}', [CitaController::class, 'show'])->whereNumber('cita');
Route::put('citas/{cita}', [CitaController::class, 'update'])->whereNumber('cita');
Route::patch('citas/{cita}/estado', [CitaController::class, 'changeEstado'])->whereNumber('cita');

Route::get('doctores', [DoctorController::class, 'index']);
Route::get('pacientes', [PacienteController::class, 'index']);
