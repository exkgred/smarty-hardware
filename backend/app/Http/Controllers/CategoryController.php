<?php

namespace App\Http\Controllers;

use App\Modules\Product\Application\UseCases\ListCategoriesUseCase;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesUseCase $listCategories,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->listCategories->handle());
    }
}
