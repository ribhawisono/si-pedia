<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDosenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lecturer = $this->route('lecturer');

        return [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $lecturer->user_id,
            'nidn'    => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'photo'   => 'nullable|image|max:10240',
        ];
    }
}
