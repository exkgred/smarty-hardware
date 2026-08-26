<?php

namespace Tests\Feature;

use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminSaleAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pos_sale_issues_invoice_and_drops_stock(): void
    {
        $admin = EloquentUserModel::query()->create([
            'name' => 'Admin',
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $customer = EloquentUserModel::query()->create([
            'name' => 'Cliente',
            'email' => 'c@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        $category = EloquentCategoryModel::query()->create(['name' => 'Tech', 'slug' => 'tech']);
        $product = EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'SSD',
            'slug' => 'ssd',
            'price' => 200,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/sales', [
            'customer_id' => $customer->id,
            'payment_method' => 'PIX',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertCreated()
            ->assertJsonPath('status', 'PAID')
            ->assertJsonPath('payment_method', 'PIX')
            ->assertJsonPath('channel', 'POS');

        $this->assertSame(4, $product->fresh()->stock_quantity);

        $this->getJson('/api/admin/invoices?type=SALE')
            ->assertOk()
            ->assertJsonFragment(['customer_name' => 'Cliente']);
    }

    public function test_admin_stock_entry_creates_note_and_increases_stock(): void
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
            'name' => 'RAM',
            'slug' => 'ram',
            'price' => 100,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/inventory/entries', [
            'supplier' => 'Kingston Dist',
            'document_number' => 'NFE-99',
            'items' => [['product_id' => $product->id, 'quantity' => 8, 'unit_cost' => 80]],
        ])->assertCreated()->assertJsonPath('invoice.number', 'NFE-99');

        $this->assertSame(10, $product->fresh()->stock_quantity);
    }
}
