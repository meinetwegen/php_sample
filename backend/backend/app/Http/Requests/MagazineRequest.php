<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Used for both creating (POST) and partially updating (PUT) a magazine. */
class MagazineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'issue_number' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price_cents' => [$required, 'integer', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'format' => ['sometimes', 'in:print,digital,both'],
            'cover_url' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
