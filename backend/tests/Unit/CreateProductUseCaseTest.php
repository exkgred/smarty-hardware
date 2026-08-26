<?php

namespace Tests\Unit;

use App\Modules\Common\Domain\DomainException;
use App\Modules\Product\Application\DTOs\CreateProductDTO;
use App\Modules\Product\Application\UseCases\CreateProductUseCase;
use App\Modules\Product\Domain\ProductEntity;
use App\Modules\Product\Domain\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var array<int, ProductEntity> */
    private array $items = [];

    private int $nextId = 1;

    public function findById(int $id): ?ProductEntity
    {
        return $this->items[$id] ?? null;
    }

    public function findBySlug(string $slug): ?ProductEntity
    {
        foreach ($this->items as $item) {
            if ($item->slug === $slug) {
                return $item;
            }
        }

        return null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        throw new \RuntimeException('not implemented');
    }

    public function save(ProductEntity $product): ProductEntity
    {
        $id = $product->id ?? $this->nextId++;
        $saved = new ProductEntity(
            id: $id,
            categoryId: $product->categoryId,
            name: $product->name,
            slug: $product->slug,
            description: $product->description,
            price: $product->price,
            stockQuantity: $product->stockQuantity,
            imageUrl: $product->imageUrl,
            isActive: $product->isActive,
        );
        $this->items[$id] = $saved;

        return $saved;
    }

    public function delete(int $id): bool
    {
        unset($this->items[$id]);

        return true;
    }

    public function listActive(int $limit = 100): array
    {
        return array_values($this->items);
    }
}

class CreateProductUseCaseTest extends TestCase
{
    public function test_creates_product_and_rejects_duplicate_slug(): void
    {
        $repository = new InMemoryProductRepository;
        $useCase = new CreateProductUseCase($repository);

        $created = $useCase->handle(new CreateProductDTO(1, 'Fone', 'fone', 10.0, 5));
        $this->assertSame(1, $created->id);

        $this->expectException(DomainException::class);
        $useCase->handle(new CreateProductDTO(1, 'Fone 2', 'fone', 12.0, 1));
    }
}
