<?php

namespace Tests\Feature;

use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_auth_→_401(): void
    {
        $this->postJson('/api/orders/checkout', [
            'items' => [['product_id' => 1, 'quantity' => 1]],
            'shippingCost' => 15,
        ])->assertUnauthorized();
    }

    public function test_checkout_reserves_stock_and_sandbox_marks_paid(): void
    {
        $user = EloquentUserModel::query()->create([
            'name' => 'Cliente',
            'email' => 'c@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        $category = EloquentCategoryModel::query()->create(['name' => 'Tech', 'slug' => 'tech']);
        $product = EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'Mouse',
            'slug' => 'mouse',
            'price' => 50,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'shippingCost' => 15,
            'payment_method' => 'BOLETO',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'PAID')
            ->assertJsonPath('total', 115)
            ->assertJsonPath('payment_method', 'BOLETO');

        $this->assertSame(1, $product->fresh()->stock_quantity);

        $this->getJson('/api/orders/my')->assertOk()->assertJsonCount(1);
    }

    public function test_checkout_insufficient_stock_→_422(): void
    {
        $user = EloquentUserModel::query()->create([
            'name' => 'Cliente',
            'email' => 'c@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        $category = EloquentCategoryModel::query()->create(['name' => 'Tech', 'slug' => 'tech']);
        $product = EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'Mouse',
            'slug' => 'mouse',
            'price' => 50,
            'stock_quantity' => 1,
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/orders/checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 5]],
            'shippingCost' => 0,
        ])->assertStatus(422);
    }
}
