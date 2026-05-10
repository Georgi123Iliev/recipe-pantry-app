<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\PantryItem;
use App\Models\Recipe;
use App\Models\User;
use App\Services\PantryAlgorithmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PantryAlgorithmTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private IngredientCategory $category;
    private Ingredient $flour;
    private Ingredient $eggs;
    private Ingredient $milk;
    private Ingredient $rice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create();
        $this->category = IngredientCategory::create(['name' => 'Основни']);

        $this->flour = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name'      => 'Брашно',
            'base_unit' => 'g',
        ]);

        $this->eggs = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name'      => 'Яйца',
            'base_unit' => 'бр.',
        ]);

        $this->milk = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name'      => 'Мляко',
            'base_unit' => 'ml',
        ]);

        $this->rice = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name'      => 'Ориз',
            'base_unit' => 'g',
        ]);

        // Pantry: Брашно, Яйца, Ориз (no milk). Quantity is a placeholder — not used by the algorithm.
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->flour->id, 'quantity' => 0]);
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->eggs->id,  'quantity' => 0]);
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->rice->id,  'quantity' => 0]);
    }

    public function test_recipe_with_zero_matching_ingredients_is_excluded(): void
    {
        // Only uses Мляко — not in pantry.
        $this->createRecipeWithIngredients('Само мляко', [$this->milk->id]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $this->assertEmpty($results->pluck('id')->toArray());
    }

    public function test_recipes_are_ordered_by_matched_count_descending(): void
    {
        // Recipe A: 2 matching ingredients (Брашно + Яйца)
        $recipeA = $this->createRecipeWithIngredients('Рецепта A', [
            $this->flour->id,
            $this->eggs->id,
        ]);

        // Recipe B: 1 matching ingredient (Ориз)
        $recipeB = $this->createRecipeWithIngredients('Рецепта B', [
            $this->rice->id,
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $ids = $results->pluck('id')->toArray();

        $this->assertContains($recipeA->id, $ids);
        $this->assertContains($recipeB->id, $ids);

        // Recipe A (2 matches) must appear before Recipe B (1 match).
        $this->assertLessThan(
            array_search($recipeB->id, $ids),
            array_search($recipeA->id, $ids)
        );
    }

    public function test_matched_count_is_correct(): void
    {
        // Брашно + Яйца + Мляко — user has Брашно and Яйца but not Мляко → matched_count = 2
        $recipe = $this->createRecipeWithIngredients('Рецепта с мляко', [
            $this->flour->id,
            $this->eggs->id,
            $this->milk->id,
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $found = $results->firstWhere('id', $recipe->id);
        $this->assertNotNull($found);
        $this->assertEquals(2, $found->matched_count);
        $this->assertEquals(3, $found->total_ingredient_count);
    }

    public function test_full_match_recipe_has_matched_count_equal_to_total(): void
    {
        // Брашно + Яйца — user has both → matched_count === total_ingredient_count
        $recipe = $this->createRecipeWithIngredients('Перфектна', [
            $this->flour->id,
            $this->eggs->id,
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $found = $results->firstWhere('id', $recipe->id);
        $this->assertNotNull($found);
        $this->assertEquals($found->total_ingredient_count, $found->matched_count);
    }

    public function test_empty_pantry_returns_empty_results(): void
    {
        $emptyUser = User::factory()->create();
        $this->createRecipeWithIngredients('Рецепта', [$this->flour->id]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($emptyUser);

        $this->assertEmpty($results->toArray());
    }

    public function test_suggestions_endpoint_returns_ranked_list(): void
    {
        $this->createRecipeWithIngredients('Рецепта', [$this->flour->id]);

        $response = $this->actingAs($this->user)
            ->get(route('pantry.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pantry/Index')
            ->has('suggestions')
        );
    }

    private function createRecipeWithIngredients(string $title, array $ingredientIds): Recipe
    {
        $recipe = Recipe::create([
            'user_id'      => $this->user->id,
            'title'        => $title,
            'description'  => 'Тест',
            'instructions' => 'Тестови инструкции',
            'prep_time'    => 10,
            'cook_time'    => 20,
            'servings'     => 4,
        ]);

        foreach ($ingredientIds as $ingredientId) {
            $recipe->ingredients()->attach($ingredientId, [
                'display_quantity'    => 1,
                'display_unit'        => 'бр.',
                'normalized_quantity' => 1,
            ]);
        }

        return $recipe;
    }
}
