import os

base_dir = '/home/admlocal/Documentos/porti/marketplace/backend'

files = {
    'app/Modules/User/Domain/UserEntity.php': r'''<?php
namespace App\Modules\User\Domain;

use App\Modules\Common\Domain\EntityInterface;

class UserEntity implements EntityInterface
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $password,
        private string $role,
        private ?string $created_at = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string { return $this->role; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at,
        ];
    }
}
''',
    'app/Modules/User/Domain/UserRepositoryInterface.php': r'''<?php
namespace App\Modules\User\Domain;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;
    public function findByEmail(string $email): ?UserEntity;
    public function save(UserEntity $user): UserEntity;
    public function delete(int $id): bool;
}
''',
    'app/Modules/User/Application/UseCases/RegisterCustomerUseCase.php': r'''<?php
namespace App\Modules\User\Application\UseCases;

use App\Modules\User\Domain\UserRepositoryInterface;
use App\Modules\User\Domain\UserEntity;
use App\Modules\User\Application\DTOs\RegisterCustomerDTO;
use DomainException;

class RegisterCustomerUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function handle(RegisterCustomerDTO $dto): UserEntity
    {
        if ($this->repository->findByEmail($dto->email)) {
            throw new DomainException('Email already exists');
        }
        $user = new UserEntity(null, $dto->name, $dto->email, password_hash($dto->password, PASSWORD_BCRYPT), 'customer');
        return $this->repository->save($user);
    }
}
''',
    'app/Modules/Product/Domain/ProductEntity.php': r'''<?php
namespace App\Modules\Product\Domain;

use App\Modules\Common\Domain\EntityInterface;

class ProductEntity implements EntityInterface
{
    public function __construct(
        private ?int $id,
        private int $categoryId,
        private string $name,
        private string $slug,
        private ?string $description,
        private float $price,
        private int $stockQuantity,
        private ?string $imageUrl,
        private bool $isActive,
        private ?string $createdAt = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getDescription(): ?string { return $this->description; }
    public function getPrice(): float { return $this->price; }
    public function getStockQuantity(): int { return $this->stockQuantity; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function getIsActive(): bool { return $this->isActive; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    public function isInStock(): bool
    {
        return $this->stockQuantity > 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stockQuantity,
            'image_url' => $this->imageUrl,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
        ];
    }
}
''',
    'app/Providers/ModuleServiceProvider.php': r'''<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings de repositórios e serviços
        $this->app->bind(\App\Modules\User\Domain\UserRepositoryInterface::class, \App\Modules\User\Infrastructure\EloquentUserRepository::class);
        $this->app->singleton(\App\Modules\Product\Domain\ProductRepositoryInterface::class, \App\Modules\Product\Infrastructure\EloquentProductRepository::class);
        $this->app->bind(\App\Modules\Inventory\Domain\InventoryServiceInterface::class, \App\Modules\Inventory\Infrastructure\EloquentInventoryService::class);
        $this->app->bind(\App\Modules\Order\Domain\OrderRepositoryInterface::class, \App\Modules\Order\Infrastructure\EloquentOrderRepository::class);
        $this->app->bind(\App\Modules\Payment\Domain\TransactionRepositoryInterface::class, \App\Modules\Payment\Infrastructure\EloquentTransactionRepository::class);
        $this->app->bind(\App\Modules\Chatbot\Domain\ConversationRepositoryInterface::class, \App\Modules\Chatbot\Infrastructure\EloquentConversationRepository::class);
        $this->app->bind(\App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface::class, \App\Modules\Chatbot\Infrastructure\EloquentKnowledgeRepository::class);
        $this->app->bind(\App\Modules\Payment\Domain\PaymentGatewayInterface::class, \App\Modules\Payment\Infrastructure\StripeGateway::class);
        $this->app->bind(\App\Modules\Chatbot\Domain\AIServiceInterface::class, \App\Modules\Chatbot\Infrastructure\GeminiAIService::class);
    }
}
'''
}

for filepath, content in files.items():
    full_path = os.path.join(base_dir, filepath)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)

print('Generated base modules.')
