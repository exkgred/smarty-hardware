<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Modules\User\Application\DTOs\LoginDTO;
use App\Modules\User\Application\DTOs\RegisterCustomerDTO;
use App\Modules\User\Application\UseCases\AuthenticateUseCase;
use App\Modules\User\Application\UseCases\RegisterCustomerUseCase;
use App\Modules\User\Application\UseCases\UpdateProfileUseCase;
use App\Modules\User\Domain\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterCustomerUseCase $registerCustomer,
        private readonly AuthenticateUseCase $authenticate,
        private readonly UserRepositoryInterface $users,
        private readonly UpdateProfileUseCase $updateProfile,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registerCustomer->handle(new RegisterCustomerDTO($request->validated()));
        $token = $this->users->createApiToken((int) $user->getId(), 'auth');

        return response()->json([
            'token' => $token,
            'user' => $user->toArray(),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authenticate->handle(new LoginDTO(
            $request->validated('email'),
            $request->validated('password'),
        ));
        $token = $this->users->createApiToken((int) $user->getId(), 'auth');

        return response()->json([
            'token' => $token,
            'user' => $user->toArray(),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->users->findById((int) $request->user()->id);

        return response()->json($user?->toArray() ?? [
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'role' => $request->user()->role,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->updateProfile->handle((int) $request->user()->id, $request->validated());

        return response()->json($user->toArray());
    }

    public function logout(Request $request): JsonResponse
    {
        $this->users->revokeCurrentToken($request->user());

        return response()->json(['message' => 'Logout realizado.']);
    }
}
