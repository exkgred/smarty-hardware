<?php

namespace App\Providers;

use App\Modules\Chatbot\Domain\AIServiceInterface;
use App\Modules\Chatbot\Domain\ConversationRepositoryInterface;
use App\Modules\Chatbot\Domain\KnowledgeRepositoryInterface;
use App\Modules\Chatbot\Infrastructure\EloquentConversationRepository;
use App\Modules\Chatbot\Infrastructure\EloquentKnowledgeRepository;
use App\Modules\Chatbot\Infrastructure\GeminiAIService;
use App\Modules\Invoice\Domain\InvoiceRepositoryInterface;
use App\Modules\Invoice\Infrastructure\EloquentInvoiceRepository;
use App\Modules\Inventory\Domain\InventoryServiceInterface;
use App\Modules\Inventory\Infrastructure\EloquentInventoryService;
use App\Modules\Order\Domain\OrderRepositoryInterface;
use App\Modules\Order\Infrastructure\EloquentOrderRepository;
use App\Modules\Payment\Domain\PaymentGatewayInterface;
use App\Modules\Payment\Domain\TransactionRepositoryInterface;
use App\Modules\Payment\Infrastructure\EloquentTransactionRepository;
use App\Modules\Payment\Domain\SavedCardRepositoryInterface;
use App\Modules\Payment\Infrastructure\EloquentSavedCardRepository;
use App\Modules\Payment\Infrastructure\FakePaymentGateway;
use App\Modules\Payment\Infrastructure\StripeGateway;
use App\Modules\Product\Domain\CategoryRepositoryInterface;
use App\Modules\Product\Domain\ProductRepositoryInterface;
use App\Modules\Product\Infrastructure\EloquentCategoryRepository;
use App\Modules\Product\Infrastructure\EloquentProductRepository;
use App\Modules\User\Domain\UserRepositoryInterface;
use App\Modules\User\Infrastructure\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(InventoryServiceInterface::class, EloquentInventoryService::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, EloquentTransactionRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, EloquentConversationRepository::class);
        $this->app->bind(KnowledgeRepositoryInterface::class, EloquentKnowledgeRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, EloquentInvoiceRepository::class);
        $this->app->bind(SavedCardRepositoryInterface::class, EloquentSavedCardRepository::class);
        $this->app->bind(AIServiceInterface::class, GeminiAIService::class);

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            if (filled(config('services.stripe.secret'))) {
                return $app->make(StripeGateway::class);
            }

            return $app->make(FakePaymentGateway::class);
        });
    }
}
