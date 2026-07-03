<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isAdmin = auth()->user()->role === 'admin';

        $rules = [
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'content'          => 'required|string',
            'image'            => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp',
            'tags'             => 'nullable|string|max:500',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords'    => 'nullable|string|max:300',
            'canonical_url'    => 'nullable|url',
        ];

        if ($isAdmin) {
            $rules['writer']     = 'required|string|max:255';
            $rules['status']     = 'required|in:active,draft,archived';
            $rules['created_at'] = 'required|date';
        }

        return $rules;
    }
}
