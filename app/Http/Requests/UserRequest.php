<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $editing = $this->route('user') !== null;

        return [
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($this->route('user'))],
            'full_name' => ['required', 'string', 'max:100'],
            // required when creating; optional (leave blank to keep) when editing
            'password' => [$editing ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],
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
            'username.regex' => 'The username may only contain letters, numbers, dots, dashes and underscores.',
            'avatar.max' => 'The avatar may not be larger than 2 MB.',
            'avatar.mimes' => 'The avatar must be a JPG, PNG or WEBP file.',
            'avatar.dimensions' => 'The avatar must be between 50×50 and 4000×4000 pixels.',
        ];
    }
}
