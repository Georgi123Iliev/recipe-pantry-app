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

        $this->user = User::factory()->create();
        $this->category = IngredientCategory::create(['name' => 'Основни']);

        $this->flour = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Брашно',
            'base_unit' => 'g',
        ]);

        $this->eggs = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Яйца',
            'base_unit' => 'бр.',
        ]);

        $this->milk = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Мляко',
            'base_unit' => 'ml',
        ]);

        $this->rice = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Ориз',
            'base_unit' => 'g',
        ]);

        // Pantry: Брашно 1000g, Яйца 6 бр., Ориз 500g (no milk)
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->flour->id, 'quantity' => 1000]);
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->eggs->id, 'quantity' => 6]);
        PantryItem::create(['user_id' => $this->user->id, 'ingredient_id' => $this->rice->id, 'quantity' => 500]);
    }

    public function test_perfect_match_recipe_is_identified(): void
    {
        // Recipe 1: Брашно 500g + Яйца 2 бр. — user has enough
        $recipe1 = $this->createRecipeWithIngredients('Рецепта 1', [
            $this->flour->id => ['display_quantity' => 500, 'display_unit' => 'g', 'normalized_quantity' => 500],
            $this->eggs->id  => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => 2],
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $perfectIds = $results['perfect']->pluck('id')->toArray();
        $this->assertContains($recipe1->id, $perfectIds);
    }

    public function test_partial_match_recipe_is_identified(): void
    {
        // Recipe 2: Брашно 500g + Яйца 2 бр. + Мляко 200ml — user lacks milk
        $recipe2 = $this->createRecipeWithIngredients('Рецепта 2', [
            $this->flour->id => ['display_quantity' => 500, 'display_unit' => 'g', 'normalized_quantity' => 500],
            $this->eggs->id  => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => 2],
            $this->milk->id  => ['display_quantity' => 200, 'display_unit' => 'ml', 'normalized_quantity' => 200],
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $partialIds = $results['partial']->pluck('id')->toArray();
        $this->assertContains($recipe2->id, $partialIds);

        $partialRecipe = $results['partial']->firstWhere('id', $recipe2->id);
        $this->assertEquals(1, $partialRecipe->missing_ingredients_count);
    }

    public function test_perfect_and_partial_are_separated_correctly(): void
    {
        $recipe1 = $this->createRecipeWithIngredients('Рецепта 1', [
            $this->flour->id => ['display_quantity' => 500, 'display_unit' => 'g', 'normalized_quantity' => 500],
            $this->eggs->id  => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => 2],
        ]);

        $recipe2 = $this->createRecipeWithIngredients('Рецепта 2', [
            $this->flour->id => ['display_quantity' => 500, 'display_unit' => 'g', 'normalized_quantity' => 500],
            $this->eggs->id  => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => 2],
            $this->milk->id  => ['display_quantity' => 200, 'display_unit' => 'ml', 'normalized_quantity' => 200],
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $perfectIds = $results['perfect']->pluck('id')->toArray();
        $partialIds = $results['partial']->pluck('id')->toArray();

        $this->assertContains($recipe1->id, $perfectIds);
        $this->assertNotContains($recipe1->id, $partialIds);

        $this->assertContains($recipe2->id, $partialIds);
        $this->assertNotContains($recipe2->id, $perfectIds);
    }

    public function test_recipe_with_more_than_3_missing_is_excluded(): void
    {
        $extra1 = Ingredient::create(['ingredient_category_id' => $this->category->id, 'name' => 'Масло', 'base_unit' => 'g']);
        $extra2 = Ingredient::create(['ingredient_category_id' => $this->category->id, 'name' => 'Захар', 'base_unit' => 'g']);
        $extra3 = Ingredient::create(['ingredient_category_id' => $this->category->id, 'name' => 'Сол', 'base_unit' => 'g']);

        $recipe = $this->createRecipeWithIngredients('Сложна рецепта', [
            $this->milk->id => ['display_quantity' => 200, 'display_unit' => 'ml', 'normalized_quantity' => 200],
            $extra1->id     => ['display_quantity' => 100, 'display_unit' => 'g', 'normalized_quantity' => 100],
            $extra2->id     => ['display_quantity' => 50, 'display_unit' => 'g', 'normalized_quantity' => 50],
            $extra3->id     => ['display_quantity' => 10, 'display_unit' => 'g', 'normalized_quantity' => 10],
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $allIds = $results['perfect']->pluck('id')
            ->merge($results['partial']->pluck('id'))
            ->toArray();

        $this->assertNotContains($recipe->id, $allIds);
    }

    public function test_insufficient_quantity_counts_as_missing(): void
    {
        // User has 1000g flour, recipe needs 2000g
        $recipe = $this->createRecipeWithIngredients('Много брашно', [
            $this->flour->id => ['display_quantity' => 2000, 'display_unit' => 'g', 'normalized_quantity' => 2000],
        ]);

        $service = app(PantryAlgorithmService::class);
        $results = $service->getResults($this->user);

        $perfectIds = $results['perfect']->pluck('id')->toArray();
        $partialIds = $results['partial']->pluck('id')->toArray();

        $this->assertNotContains($recipe->id, $perfectIds);
        $this->assertContains($recipe->id, $partialIds);
    }

    public function test_suggestions_endpoint_returns_results(): void
    {
        $this->createRecipeWithIngredients('Перфектна', [
            $this->flour->id => ['display_quantity' => 500, 'display_unit' => 'g', 'normalized_quantity' => 500],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('pantry.suggestions'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pantry/Suggestions')
            ->has('perfect')
            ->has('partial')
        );
    }

    private function createRecipeWithIngredients(string $title, array $ingredients): Recipe
    {
        $recipe = Recipe::create([
            'user_id' => $this->user->id,
            'title' => $title,
            'description' => 'Тест',
            'instructions' => 'Тестови инструкции',
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4,
        ]);

        foreach ($ingredients as $ingredientId => $pivotData) {
            $recipe->ingredients()->attach($ingredientId, $pivotData);
        }

        return $recipe;
    }
}
