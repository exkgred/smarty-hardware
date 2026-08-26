<?php

namespace App\Modules\Common\Domain;

class AddressData
{
    public function __construct(
        public readonly ?string $zip = null,
        public readonly ?string $street = null,
        public readonly ?string $number = null,
        public readonly ?string $complement = null,
        public readonly ?string $neighborhood = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $zip = preg_replace('/\D/', '', (string) ($data['zip'] ?? $data['cep'] ?? '')) ?: null;

        return new self(
            zip: $zip,
            street: self::nullable($data['street'] ?? $data['logradouro'] ?? null),
            number: self::nullable($data['number'] ?? $data['numero'] ?? null),
            complement: self::nullable($data['complement'] ?? $data['complemento'] ?? null),
            neighborhood: self::nullable($data['neighborhood'] ?? $data['bairro'] ?? null),
            city: self::nullable($data['city'] ?? $data['localidade'] ?? null),
            state: self::nullable($data['state'] ?? $data['uf'] ?? null),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'zip' => $this->formattedZip(),
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
        ];
    }

    public function formattedZip(): ?string
    {
        if ($this->zip === null || strlen($this->zip) !== 8) {
            return $this->zip;
        }

        return substr($this->zip, 0, 5).'-'.substr($this->zip, 5);
    }

    public function line(): string
    {
        $parts = array_filter([
            $this->street,
            $this->number,
            $this->complement,
            $this->neighborhood,
            $this->city && $this->state ? $this->city.'/'.$this->state : ($this->city ?? $this->state),
            $this->formattedZip(),
        ], static fn ($part) => $part !== null && $part !== '');

        return implode(', ', $parts);
    }

    public function isEmpty(): bool
    {
        return $this->zip === null && $this->street === null && $this->city === null;
    }

    private static function nullable(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
