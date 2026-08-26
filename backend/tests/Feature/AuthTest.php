<?php

namespace Tests\Feature;

use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_me_→_returns_token_and_profile(): void
    {
        $register = $this->postJson('/api/register', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret12',
        ]);

        $register->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'email', 'role']]);
        $this->assertSame('customer', $register->json('user.role'));

        $me = $this->withToken($register->json('token'))->getJson('/api/me');
        $me->assertOk()->assertJsonPath('email', 'ana@example.com');
    }

    public function test_login_invalid_credentials_→_401(): void
    {
        $this->postJson('/api/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong',
        ])->assertStatus(401);
    }

    public function test_duplicate_email_→_409(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret12',
        ])->assertCreated();

        $this->postJson('/api/register', [
            'name' => 'Ana 2',
            'email' => 'ana@example.com',
            'password' => 'secret12',
        ])->assertStatus(409);
    }

    public function test_customer_cannot_access_admin_→_403(): void
    {
        $user = EloquentUserModel::query()->create([
            'name' => 'Cliente',
            'email' => 'c@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/dashboard')->assertForbidden();
    }
}
