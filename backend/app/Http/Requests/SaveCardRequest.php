<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCardRequest extends FormRequest
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
        return [
            'number' => ['required', 'string'],
            'holder_name' => ['required', 'string', 'max:80'],
            'exp_month' => ['required', 'integer', 'min:1', 'max:12'],
            'exp_year' => ['required', 'integer'],
            'cvv' => ['required', 'string', 'max:4'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
