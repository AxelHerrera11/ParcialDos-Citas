<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeEstadoRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Resources\CitaResource;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CitaController extends Controller
{
    public function __construct(private readonly CitaService $citas) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return CitaResource::collection($this->citas->list($request->only('doctor_id', 'paciente_id', 'desde', 'hasta')));
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        $cita = $this->citas->create($request->validated());

        return (new CitaResource($cita->load(['paciente', 'doctor'])))->response()->setStatusCode(201);
    }

    public function show(int $id): CitaResource
    {
        return new CitaResource($this->citas->findOrFail($id));
    }

    public function update(UpdateCitaRequest $request, int $id): CitaResource
    {
        return new CitaResource($this->citas->update($this->citas->findOrFail($id), $request->validated()));
    }

    public function changeEstado(ChangeEstadoRequest $request, int $id): CitaResource
    {
        return new CitaResource($this->citas->changeEstado($this->citas->findOrFail($id), $request->validated('estado')));
    }
}
