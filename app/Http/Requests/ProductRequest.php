<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // access is already restricted by the "auth" middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['stock_quantity' => 'stock quantity'];
    }

    public function messages(): array
    {
        return [
            'price.decimal' => 'The price may have at most 2 decimal places.',
            'image.max' => 'The image may not be larger than 2 MB.',
            'image.mimes' => 'The image must be a JPG, PNG or WEBP file.',
            'image.dimensions' => 'The image must be between 50×50 and 4000×4000 pixels.',
        ];
    }
}
