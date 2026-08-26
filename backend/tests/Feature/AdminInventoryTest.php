<?php

namespace Tests\Feature;

use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_adjusts_stock_and_sees_alerts(): void
    {
        $admin = EloquentUserModel::query()->create([
            'name' => 'Admin',
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $category = EloquentCategoryModel::query()->create(['name' => 'Tech', 'slug' => 'tech']);
        $product = EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'Webcam',
            'slug' => 'webcam',
            'price' => 200,
            'stock_quantity' => 10,
            'stock_alert_threshold' => 5,
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/inventory/adjust', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => 7,
            'reason' => 'Ajuste de inventário',
        ])->assertCreated();

        $this->assertSame(3, $product->fresh()->stock_quantity);

        $this->getJson('/api/admin/inventory/'.$product->id.'/movements')
            ->assertOk()
            ->assertJsonPath('0.type', 'OUT');

        $this->getJson('/api/admin/inventory/alerts')
            ->assertOk()
            ->assertJsonFragment(['product_name' => 'Webcam']);
    }
}
