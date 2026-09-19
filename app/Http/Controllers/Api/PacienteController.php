<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Services\CitaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PacienteController extends Controller
{
    public function __construct(private readonly CitaService $pacientes) {}

    public function index(): AnonymousResourceCollection
    {
        return PacienteResource::collection($this->pacientes->pacientes());
    }
}
