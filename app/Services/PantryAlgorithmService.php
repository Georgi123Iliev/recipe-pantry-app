<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;

class PantryAlgorithmService
{
    /**
     * Return all recipes that share at least one ingredient with the user's pantry,
     * ordered by the number of matching ingredients descending.
     * Each recipe carries matched_count and ingredients_count virtual attributes.
     */
    public function getResults(User $user): Collection
    {
        $pantryIngredientIds = $user->pantryItems()->pluck('ingredient_id');

        if ($pantryIngredientIds->isEmpty()) {
            return collect();
        }

        return Recipe::withCount([
            'ingredients as matched_count' => function ($q) use ($pantryIngredientIds) {
                $q->whereIn('ingredients.id', $pantryIngredientIds);
            },
            'ingredients as total_ingredient_count',
        ])
        ->whereHas('ingredients', function ($q) use ($pantryIngredientIds) {
            $q->whereIn('ingredients.id', $pantryIngredientIds);
        })
        ->orderByDesc('matched_count')
        ->with(['ingredients', 'images', 'user'])
        ->get();
    }
}
