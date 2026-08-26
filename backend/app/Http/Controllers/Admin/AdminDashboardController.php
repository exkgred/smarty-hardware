<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Application\UseCases\GetDashboardStatsUseCase;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardStatsUseCase $dashboard,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json($this->dashboard->handle());
    }
}
