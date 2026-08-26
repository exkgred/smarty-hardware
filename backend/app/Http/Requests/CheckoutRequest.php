<?php

namespace App\Http\Requests;

use App\Modules\Payment\Domain\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $cardRequired = strtoupper((string) $this->input('payment_method')) === 'CREDIT_CARD'
            && ! $this->filled('card_id');

        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shippingCost' => ['nullable', 'numeric', 'min:0'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'shipping_name' => ['nullable', 'string', 'max:255'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'array'],
            'address.zip' => ['nullable', 'string', 'max:9'],
            'address.street' => ['nullable', 'string', 'max:150'],
            'address.number' => ['nullable', 'string', 'max:20'],
            'address.complement' => ['nullable', 'string', 'max:80'],
            'address.neighborhood' => ['nullable', 'string', 'max:80'],
            'address.city' => ['nullable', 'string', 'max:80'],
            'address.state' => ['nullable', 'string', 'max:2'],
            'save_address' => ['sometimes', 'boolean'],
            'payment_method' => ['nullable', 'string', Rule::in(array_column(PaymentMethodEnum::cases(), 'value'))],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'card_id' => ['nullable', 'integer'],
            'save_card' => ['sometimes', 'boolean'],
            'card' => [$cardRequired ? 'required' : 'nullable', 'array'],
            'card.number' => [$cardRequired ? 'required' : 'nullable', 'string'],
            'card.holder_name' => [$cardRequired ? 'required' : 'nullable', 'string', 'max:80'],
            'card.exp_month' => [$cardRequired ? 'required' : 'nullable', 'integer', 'min:1', 'max:12'],
            'card.exp_year' => [$cardRequired ? 'required' : 'nullable', 'integer'],
            'card.cvv' => [$cardRequired ? 'required' : 'nullable', 'string', 'max:4'],
        ];
    }
}
