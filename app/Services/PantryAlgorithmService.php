<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;

class PantryAlgorithmService
{
    /**
     * @return array{perfect: Collection, partial: Collection}
     */
    public function getResults(User $user): array
    {
        $pantry = $user->pantryItems()
            ->pluck('quantity', 'ingredient_id');

        $perfectIds = $this->getPerfectMatchIds($pantry);
        $partial = $this->getPartialMatches($pantry, $perfectIds);

        $perfect = Recipe::with(['ingredients', 'images', 'user'])
            ->whereIn('id', $perfectIds)
            ->get();

        return [
            'perfect' => $perfect,
            'partial' => $partial,
        ];
    }

    /**
     * Perfect match: every ingredient in the recipe exists in the pantry
     * with sufficient quantity (normalized_quantity <= pantry quantity).
     */
    private function getPerfectMatchIds(Collection $pantry): array
    {
        return Recipe::whereDoesntHave('ingredients', function ($query) use ($pantry) {
            $query->where(function ($q) use ($pantry) {
                foreach ($pantry as $ingredientId => $qty) {
                    $q->orWhere(function ($sub) use ($ingredientId, $qty) {
                        $sub->where('ingredient_id', $ingredientId)
                            ->where('normalized_quantity', '>', $qty);
                    });
                }

                $q->orWhereNotIn('ingredient_id', $pantry->keys()->toArray());
            });
        })->pluck('id')->toArray();
    }

    /**
     * Partial match: recipes with at most 3 missing/insufficient ingredients.
     * Excludes perfect matches. Ordered by missing count ascending.
     */
    private function getPartialMatches(Collection $pantry, array $excludeIds): Collection
    {
        $recipes = Recipe::with(['ingredients', 'images', 'user'])
            ->whereNotIn('id', $excludeIds)
            ->get();

        return $recipes->map(function ($recipe) use ($pantry) {
            $missingCount = 0;

            foreach ($recipe->ingredients as $ingredient) {
                $pantryQty = $pantry->get($ingredient->id, 0);

                if ($pantryQty < $ingredient->pivot->normalized_quantity) {
                    $missingCount++;
                }
            }

            $recipe->missing_ingredients_count = $missingCount;

            return $recipe;
        })
        ->filter(fn ($recipe) => $recipe->missing_ingredients_count >= 1 && $recipe->missing_ingredients_count <= 3)
        ->sortBy('missing_ingredients_count')
        ->values();
    }
}
