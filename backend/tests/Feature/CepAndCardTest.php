<?php

namespace Tests\Feature;

use App\Modules\Payment\Infrastructure\EloquentSavedCardModel;
use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CepAndCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_cep_lookup_fills_address_from_viacep(): void
    {
        Http::fake([
            'viacep.com.br/*' => Http::response([
                'cep' => '01001-000',
                'logradouro' => 'Praça da Sé',
                'complemento' => 'lado ímpar',
                'bairro' => 'Sé',
                'localidade' => 'São Paulo',
                'uf' => 'SP',
            ]),
        ]);

        $this->getJson('/api/cep/01001-000')
            ->assertOk()
            ->assertJsonPath('zip', '01001-000')
            ->assertJsonPath('street', 'Praça da Sé')
            ->assertJsonPath('neighborhood', 'Sé')
            ->assertJsonPath('city', 'São Paulo')
            ->assertJsonPath('state', 'SP');
    }

    public function test_cep_not_found_→_404(): void
    {
        Http::fake([
            'viacep.com.br/*' => Http::response(['erro' => true]),
        ]);

        $this->getJson('/api/cep/00000000')
            ->assertStatus(404)
            ->assertJsonPath('message', 'CEP não encontrado.');
    }

    public function test_save_card_keeps_only_last_four_never_pan(): void
    {
        $user = $this->customer();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/me/cards', [
            'number' => '4242 4242 4242 4242',
            'holder_name' => 'CLIENTE SMARTY',
            'exp_month' => 12,
            'exp_year' => 2030,
            'cvv' => '123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('last_four', '4242')
            ->assertJsonPath('brand', 'visa')
            ->assertJsonMissingPath('token')
            ->assertJsonMissingPath('number');

        $this->assertDatabaseHas('saved_cards', [
            'user_id' => $user->id,
            'last_four' => '4242',
            'holder_name' => 'CLIENTE SMARTY',
        ]);
        $this->assertDatabaseMissing('saved_cards', [
            'last_four' => '4242424242424242',
        ]);
        $this->assertStringNotContainsString('4242424242424242', json_encode(EloquentSavedCardModel::query()->first()?->toArray()) ?: '');
        $this->assertStringNotContainsString('123', (string) EloquentSavedCardModel::query()->value('token'));
    }

    public function test_invalid_card_number_→_422(): void
    {
        Sanctum::actingAs($this->customer());

        $this->postJson('/api/me/cards', [
            'number' => '4242424242424241',
            'holder_name' => 'CLIENTE SMARTY',
            'exp_month' => 12,
            'exp_year' => 2030,
            'cvv' => '123',
        ])->assertStatus(422);
    }

    public function test_update_profile_saves_address(): void
    {
        Sanctum::actingAs($this->customer());

        $this->putJson('/api/me', [
            'name' => 'Cliente Atualizado',
            'phone' => '11999990000',
            'address' => [
                'zip' => '01310-100',
                'street' => 'Av. Paulista',
                'number' => '1000',
                'complement' => 'Cj 12',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
        ])->assertOk()
            ->assertJsonPath('name', 'Cliente Atualizado')
            ->assertJsonPath('phone', '11999990000')
            ->assertJsonPath('address.street', 'Av. Paulista')
            ->assertJsonPath('address.city', 'São Paulo');

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('address.number', '1000');
    }

    public function test_checkout_with_address_and_saved_card(): void
    {
        $user = $this->customer();
        Sanctum::actingAs($user);
        $product = $this->product();

        $card = $this->postJson('/api/me/cards', [
            'number' => '5555555555554444',
            'holder_name' => 'CLIENTE SMARTY',
            'exp_month' => 11,
            'exp_year' => 2031,
            'cvv' => '321',
        ])->assertCreated();

        $response = $this->postJson('/api/orders/checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'shippingCost' => 15,
            'name' => 'Cliente Smarty',
            'payment_method' => 'CREDIT_CARD',
            'save_address' => true,
            'address' => [
                'zip' => '01001-000',
                'street' => 'Praça da Sé',
                'number' => '10',
                'neighborhood' => 'Sé',
                'city' => 'São Paulo',
                'state' => 'SP',
            ],
            'card_id' => $card->json('id'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'PAID')
            ->assertJsonPath('payment_method', 'CREDIT_CARD')
            ->assertJsonPath('shipping_details.city', 'São Paulo')
            ->assertJsonPath('payment_receipt.last_four', '4444');

        $this->assertStringContainsString('4444', (string) $response->json('payment_receipt.instructions'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Cliente Smarty']);
        $this->assertStringNotContainsString('5555555555554444', (string) json_encode($response->json()));
    }

    private function customer(): EloquentUserModel
    {
        return EloquentUserModel::query()->create([
            'name' => 'Cliente',
            'email' => 'c@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }

    private function product(): EloquentProductModel
    {
        $category = EloquentCategoryModel::query()->create(['name' => 'Tech', 'slug' => 'tech']);

        return EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'Mouse',
            'slug' => 'mouse',
            'price' => 50,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
    }
}
