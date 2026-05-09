<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'title'        => $this->title,
            'description'  => $this->description,
            'instructions' => $this->instructions,
            'prep_time'    => $this->prep_time,
            'cook_time'    => $this->cook_time,
            'servings'     => $this->servings,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'user'         => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'ingredients'  => $this->whenLoaded('ingredients', function () {
                return $this->ingredients->map(fn ($ingredient) => [
                    'id'               => $ingredient->id,
                    'name'             => $ingredient->name,
                    'base_unit'        => $ingredient->base_unit,
                    'display_quantity' => $ingredient->pivot->display_quantity,
                    'display_unit'     => $ingredient->pivot->display_unit,
                ]);
            }),
            'images'       => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($image) => [
                    'id'  => $image->id,
                    'url' => Storage::url($image->path),
                ]);
            }),
        ];
    }
}
