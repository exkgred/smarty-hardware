<?php

namespace App\Http\Controllers;

use App\Modules\Common\Application\UseCases\LookupCepUseCase;
use Illuminate\Http\JsonResponse;

class CepController extends Controller
{
    public function __construct(private readonly LookupCepUseCase $lookupCep) {}

    public function show(string $cep): JsonResponse
    {
        return response()->json($this->lookupCep->handle($cep));
    }
}
