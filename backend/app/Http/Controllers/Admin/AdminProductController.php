<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\DTOs\UpdateProductDTO;
use App\Modules\Product\Application\UseCases\CreateProductUseCase;
use App\Modules\Product\Application\UseCases\DeleteProductUseCase;
use App\Modules\Product\Application\UseCases\ListProductsUseCase;
use App\Modules\Product\Application\UseCases\UpdateProductUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function __construct(
        private readonly ListProductsUseCase $listProducts,
        private readonly CreateProductUseCase $createProduct,
        private readonly UpdateProductUseCase $updateProduct,
        private readonly DeleteProductUseCase $deleteProduct,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category_id' => $request->query('category_id'),
            'search' => $request->query('search'),
        ];

        return response()->json(
            $this->listProducts->handle($filters, (int) $request->query('per_page', 50))
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->createProduct->handle(CreateProductDTO::fromArray($request->validated()));

        return response()->json($product->toArray(), 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->updateProduct->handle($id, UpdateProductDTO::fromArray($request->validated()));

        return response()->json($product->toArray());
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteProduct->handle($id);

        return response()->json(['message' => 'Produto excluído.']);
    }
}
