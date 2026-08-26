<?php

namespace App\Modules\Chatbot\Domain;

interface AIServiceInterface
{
    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @param  array<int, string>  $contextChunks
     */
    public function reply(string $userMessage, array $history, array $contextChunks): string;
}
