<?php

namespace App\Modules\Chatbot\Infrastructure;

use App\Modules\Chatbot\Domain\KnowledgeDocumentEntity;
use App\Modules\Product\Domain\ProductEntity;

class SimilaritySearchService
{
    /**
     * @param  array<int, KnowledgeDocumentEntity>  $documents
     * @param  array<int, ProductEntity>  $products
     * @return array<int, string>
     */
    public function relevantChunks(string $query, array $documents, array $products, int $limit = 5): array
    {
        $scored = [];

        foreach ($documents as $document) {
            $text = $document->title.' '.$document->content;
            $scored[] = ['score' => $this->score($query, $text), 'chunk' => "[{$document->type}] {$document->title}: {$document->content}"];
        }

        foreach ($products as $product) {
            $text = $product->name.' '.($product->description ?? '').' '.($product->category?->name ?? '');
            $stock = $product->isInStock() ? "estoque: {$product->stockQuantity}" : 'sem estoque';
            $chunk = "Produto {$product->name} (categoria {$product->category?->name}, R$ ".number_format($product->price, 2, ',', '.').", {$stock}). {$product->description}";
            $scored[] = ['score' => $this->score($query, $text), 'chunk' => $chunk];
        }

        usort($scored, static fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_values(array_map(
            static fn ($item) => $item['chunk'],
            array_filter(array_slice($scored, 0, $limit), static fn ($item) => $item['score'] > 0),
        ));
    }

    private function score(string $query, string $text): float
    {
        $queryTokens = $this->tokens($query);
        $textTokens = $this->tokens($text);
        if ($queryTokens === [] || $textTokens === []) {
            return 0;
        }

        $overlap = count(array_intersect($queryTokens, $textTokens));
        $boost = 0;
        $aliases = [
            'processador' => ['cpu', 'ryzen', 'intel', 'amd'],
            'placa' => ['gpu', 'rtx', 'video', 'vídeo', 'geforce'],
            'memoria' => ['ram', 'kingston', 'gskill', 'ddr'],
            'pagamento' => ['pix', 'boleto', 'cartao', 'cartão', 'credito'],
            'assistencia' => ['limpeza', 'formatacao', 'reparo', 'montagem', 'bancada'],
        ];
        foreach ($aliases as $canon => $words) {
            $inQuery = in_array($canon, $queryTokens, true) || count(array_intersect($queryTokens, $words)) > 0;
            $inText = in_array($canon, $textTokens, true) || count(array_intersect($textTokens, $words)) > 0;
            if ($inQuery && $inText) {
                $boost += 0.35;
            }
        }

        return ($overlap / count($queryTokens)) + $boost;
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $text): array
    {
        $normalized = mb_strtolower($text);
        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized) ?? $normalized;
        $parts = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stop = ['de', 'da', 'do', 'das', 'dos', 'a', 'o', 'e', 'um', 'uma', 'para', 'com', 'em', 'no', 'na', 'os', 'as', 'que', 'qual', 'voces', 'você', 'tem', 'the'];

        return array_values(array_filter($parts, static fn ($token) => mb_strlen($token) > 2 && ! in_array($token, $stop, true)));
    }
}
