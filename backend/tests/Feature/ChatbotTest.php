<?php

namespace Tests\Feature;

use App\Modules\Chatbot\Infrastructure\EloquentKnowledgeModel;
use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_answers_from_catalog_and_knowledge(): void
    {
        $category = EloquentCategoryModel::query()->create(['name' => 'Eletrônicos', 'slug' => 'eletronicos']);
        EloquentProductModel::query()->create([
            'category_id' => $category->id,
            'name' => 'Fone Bluetooth Pulse',
            'slug' => 'fone-bluetooth-pulse',
            'description' => 'Fone sem fio com cancelamento de ruído.',
            'price' => 199.9,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
        EloquentKnowledgeModel::query()->create([
            'title' => 'Política de troca',
            'type' => 'policy',
            'content' => 'Você tem 7 dias corridos após o recebimento para solicitar troca.',
        ]);

        $response = $this->postJson('/api/chat/send', [
            'sessionId' => 'sess-test-1',
            'message' => 'Vocês vendem fone bluetooth? Qual a política de troca?',
        ]);

        $response->assertOk()->assertJsonStructure(['message']);
        $this->assertNotEmpty($response->json('message'));
    }

    public function test_chat_answers_payment_intent(): void
    {
        $response = $this->postJson('/api/chat/send', [
            'sessionId' => 'sess-pay-1',
            'message' => 'Quais formas de pagamento vocês aceitam? PIX e boleto?',
        ]);

        $response->assertOk();
        $this->assertStringContainsStringIgnoringCase('pix', $response->json('message'));
    }
}
