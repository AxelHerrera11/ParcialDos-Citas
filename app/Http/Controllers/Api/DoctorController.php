<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Services\CitaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorController extends Controller
{
    public function __construct(private readonly CitaService $doctores) {}

    public function index(): AnonymousResourceCollection
    {
        return DoctorResource::collection($this->doctores->doctores());
    }
}
