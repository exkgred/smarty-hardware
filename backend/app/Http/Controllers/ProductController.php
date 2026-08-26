<?php

namespace App\Http\Controllers;

use App\Modules\Product\Application\UseCases\GetProductDetailUseCase;
use App\Modules\Product\Application\UseCases\ListProductsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ListProductsUseCase $listProducts,
        private readonly GetProductDetailUseCase $getProductDetail,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => true,
            'category_id' => $request->query('category_id'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'search' => $request->query('search'),
        ];

        return response()->json(
            $this->listProducts->handle($filters, (int) $request->query('per_page', 24))
        );
    }

    public function show(string $slug): JsonResponse
    {
        return response()->json($this->getProductDetail->handle($slug)->toArray());
    }
}
