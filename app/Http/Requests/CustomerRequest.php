<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:100', Rule::unique('customers', 'email')->ignore($this->route('customer'))],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=50,min_height=50,max_width=4000,max_height=4000'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['full_name' => 'full name'];
    }

    public function messages(): array
    {
        return [
            'avatar.max' => 'The photo may not be larger than 2 MB.',
            'avatar.mimes' => 'The photo must be a JPG, PNG or WEBP file.',
            'avatar.dimensions' => 'The photo must be between 50×50 and 4000×4000 pixels.',
            'phone.regex' => 'The phone number may only contain digits, spaces, +, - and parentheses.',
        ];
    }
}
