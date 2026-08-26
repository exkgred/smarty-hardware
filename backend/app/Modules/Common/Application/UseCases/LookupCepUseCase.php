<?php

namespace App\Modules\Common\Application\UseCases;

use App\Modules\Common\Domain\AddressData;
use App\Modules\Common\Domain\DomainException;
use Illuminate\Support\Facades\Http;

class LookupCepUseCase
{
    public function handle(string $cep): array
    {
        $digits = preg_replace('/\D/', '', $cep) ?? '';
        if (strlen($digits) !== 8) {
            throw new DomainException('CEP deve ter 8 dígitos.');
        }

        $response = Http::timeout(8)->get("https://viacep.com.br/ws/{$digits}/json/");
        if (! $response->successful()) {
            throw new DomainException('Não foi possível consultar o CEP agora.');
        }

        $payload = $response->json();
        if (! is_array($payload) || ! empty($payload['erro'])) {
            throw new DomainException('CEP não encontrado.', 404);
        }

        $address = AddressData::fromArray($payload);

        return $address->toArray();
    }
}
