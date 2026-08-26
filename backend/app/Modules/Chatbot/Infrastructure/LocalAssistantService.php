<?php

namespace App\Modules\Chatbot\Infrastructure;

class LocalAssistantService
{
    /**
     * @param  array<int, string>  $chunks
     */
    public function reply(string $userMessage, array $chunks): string
    {
        $text = mb_strtolower($userMessage);

        if (preg_match('/\b(oi|olá|ola|hey|opa)\b/u', $text) || str_contains($text, 'bom dia') || str_contains($text, 'boa tarde') || str_contains($text, 'boa noite')) {
            return "Olá! Sou a Mia, assistente da Smarty Hardware.\nPosso falar de peças (Ryzen, Intel, RTX, RAM, HD), serviços da bancada e pagamento (PIX, cartão, boleto, dinheiro ou transferência).\nO que você precisa hoje?";
        }

        if ($this->matches($text, ['horario', 'horário', 'abre', 'funcionamento', 'expediente', 'aberto'])) {
            return 'Atendemos em São Paulo, segunda a sexta das 9h às 18h e sábado das 9h às 13h. Serviços de bancada podem ser agendados no catálogo (categoria Assistência).';
        }

        if ($this->matches($text, ['pagamento', 'pagar', 'pix', 'boleto', 'cartao', 'cartão', 'credito', 'crédito', 'debito', 'dinheiro', 'transferencia', 'transferência'])) {
            return "Formas de pagamento na Smarty (sandbox da loja):\n• PIX — confirma na hora\n• Cartão de crédito — 1x sandbox **** 4242\n• Cartão de débito\n• Boleto bancário\n• Dinheiro no balcão\n• Transferência TED/DOC\nNo checkout você escolhe a forma. Na loja física o admin registra a venda no painel.";
        }

        if ($this->matches($text, ['frete', 'entrega', 'prazo', 'envia', 'envio'])) {
            return 'Frete padrão de peças: R$ 15,00, prazo 3 a 7 dias úteis para capitais. Serviços de bancada não cobram frete — traga o equipamento ou retire na loja.';
        }

        if ($this->matches($text, ['troca', 'garantia', 'defeito', 'arrependimento', 'devol'])) {
            return 'Peças lacradas: 7 dias para arrependimento (CDC). Defeito: 90 dias de garantia Smarty com laudo da bancada. Serviços de assistência: 30 dias sobre o serviço executado.';
        }

        if ($this->matches($text, ['limpeza', 'formatacao', 'formatação', 'reparo', 'montagem', 'assistencia', 'assistência', 'bancada', 'pasta termica', 'pasta térmica'])) {
            return "Serviços da bancada Smarty:\n• Limpeza + pasta térmica (notebook) — R$ 179\n• Formatação com backup — R$ 199\n• Diagnóstico de placa — R$ 120 (reparo orçado à parte)\n• Montagem de PC gamer — R$ 250\n• Instalação de processador — R$ 89\n• Upgrade de RAM na loja e inspeção de PCB\nAgenda no catálogo, categoria Assistência. Sem frete.";
        }

        if ($chunks === []) {
            return 'Não encontrei isso no catálogo agora. Posso ajudar com processadores, placas de vídeo, memória, HD, gabinetes, periféricos, assistência técnica, frete e formas de pagamento.';
        }

        $lines = ["Encontrei isto na Smarty Hardware:\n"];
        foreach (array_slice($chunks, 0, 4) as $chunk) {
            $clean = trim(strip_tags($chunk));
            $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;
            $lines[] = '• '.$clean;
        }
        $lines[] = "\nSe quiser, pergunte o preço, o estoque ou a forma de pagamento.";

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, string>  $needles
     */
    private function matches(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
