<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                          => 'required|string|max:255',
            'description'                    => 'nullable|string',
            'instructions'                   => 'required|string',
            'prep_time'                      => 'required|integer|min:0',
            'cook_time'                      => 'required|integer|min:0',
            'servings'                       => 'required|integer|min:1',
            'ingredients'                    => 'required|array|min:1',
            'ingredients.*.ingredient_id'    => 'required|exists:ingredients,id',
            'ingredients.*.display_quantity' => 'required|numeric|min:0.01',
            'ingredients.*.display_unit'     => 'required|string',
            'images'                         => 'nullable|array',
            'images.*'                       => 'image|max:2048',
        ];
    }
}
