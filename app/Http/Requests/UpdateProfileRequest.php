<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:users,email,' . auth()->id(),
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'avatar'   => 'nullable|image|max:10240',
        ];
    }
}
