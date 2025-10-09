<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'is_active' => 'required|in:1,0',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'file|image|max:10240',
        ];
    }
}
