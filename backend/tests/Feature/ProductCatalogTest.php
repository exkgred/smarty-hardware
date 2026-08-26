<?php

namespace Tests\Feature;

use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_products_paginated_and_filter_by_category(): void
    {
        $electronics = EloquentCategoryModel::query()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
        ]);
        $home = EloquentCategoryModel::query()->create([
            'name' => 'Casa',
            'slug' => 'casa',
        ]);

        EloquentProductModel::query()->create([
            'category_id' => $electronics->id,
            'name' => 'Fone',
            'slug' => 'fone',
            'price' => 100,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        EloquentProductModel::query()->create([
            'category_id' => $home->id,
            'name' => 'Panela',
            'slug' => 'panela',
            'price' => 80,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);
        EloquentProductModel::query()->create([
            'category_id' => $electronics->id,
            'name' => 'Oculto',
            'slug' => 'oculto',
            'price' => 10,
            'stock_quantity' => 1,
            'is_active' => false,
        ]);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonStructure(['data', 'current_page', 'last_page']);

        $this->getJson('/api/products?category_id='.$electronics->id)
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.slug', 'fone');

        $this->getJson('/api/products/fone')
            ->assertOk()
            ->assertJsonPath('name', 'Fone');

        $this->getJson('/api/products/oculto')->assertNotFound();
        $this->getJson('/api/categories')->assertOk()->assertJsonCount(2);
    }
}
