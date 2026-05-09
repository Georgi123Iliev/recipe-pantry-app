<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecipeService
{
    public function store(array $validated, User $user): Recipe
    {
        $uploadedPaths = [];

        try {
            return DB::transaction(function () use ($validated, $user, &$uploadedPaths) {
                $recipe = $user->recipes()->create([
                    'title'        => $validated['title'],
                    'description'  => $validated['description'] ?? null,
                    'instructions' => $validated['instructions'],
                    'prep_time'    => $validated['prep_time'],
                    'cook_time'    => $validated['cook_time'],
                    'servings'     => $validated['servings'],
                ]);

                $this->syncIngredients($recipe, $validated['ingredients']);

                if (!empty($validated['images'])) {
                    foreach ($validated['images'] as $file) {
                        $path = $file->store('recipes', 'public');
                        $uploadedPaths[] = $path;
                        $recipe->images()->create(['path' => $path]);
                    }
                }

                return $recipe;
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }
    }

    public function update(array $validated, Recipe $recipe): Recipe
    {
        $uploadedPaths = [];
        $stalePaths = [];

        try {
            DB::transaction(function () use ($validated, $recipe, &$uploadedPaths, &$stalePaths) {
                $recipe->update([
                    'title'        => $validated['title'],
                    'description'  => $validated['description'] ?? null,
                    'instructions' => $validated['instructions'],
                    'prep_time'    => $validated['prep_time'],
                    'cook_time'    => $validated['cook_time'],
                    'servings'     => $validated['servings'],
                ]);

                $this->syncIngredients($recipe, $validated['ingredients']);

                $keepImageIds = $validated['keep_images'] ?? [];
                $staleImages = $recipe->images()->whereNotIn('id', $keepImageIds)->get();

                foreach ($staleImages as $image) {
                    $stalePaths[] = $image->path;
                    $image->delete();
                }

                if (!empty($validated['images'])) {
                    foreach ($validated['images'] as $file) {
                        $path = $file->store('recipes', 'public');
                        $uploadedPaths[] = $path;
                        $recipe->images()->create(['path' => $path]);
                    }
                }
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        // Physical deletion of stale files ONLY after successful commit
        foreach ($stalePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return $recipe->fresh(['ingredients', 'images']);
    }

    public function delete(Recipe $recipe): void
    {
        $imagePaths = $recipe->images->pluck('path')->toArray();

        $recipe->delete();

        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $pivotData = [];

        foreach ($ingredients as $item) {
            $normalizedQty = UnitConverter::toBaseUnit(
                (float) $item['display_quantity'],
                $item['display_unit']
            );

            $pivotData[$item['ingredient_id']] = [
                'display_quantity'    => $item['display_quantity'],
                'display_unit'       => $item['display_unit'],
                'normalized_quantity' => $normalizedQty,
            ];
        }

        $recipe->ingredients()->sync($pivotData);
    }
}
