<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private IngredientCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = IngredientCategory::create(['name' => 'Основни']);
    }

    public function test_index_returns_all_recipes_without_search(): void
    {
        $recipe1 = $this->createRecipe('Мусака');
        $recipe2 = $this->createRecipe('Баница');

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->has('recipes.data', 2)
        );
    }

    public function test_search_filters_by_recipe_title(): void
    {
        $this->createRecipe('Мусака');
        $this->createRecipe('Баница');
        $this->createRecipe('Тиквеник');

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index', ['search' => 'Мусака']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->has('recipes.data', 1)
            ->where('recipes.data.0.title', 'Мусака')
            ->where('filters.search', 'Мусака')
        );
    }

    public function test_search_filters_by_ingredient_name(): void
    {
        $flour = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Брашно',
            'base_unit' => 'g',
        ]);

        $eggs = Ingredient::create([
            'ingredient_category_id' => $this->category->id,
            'name' => 'Яйца',
            'base_unit' => 'бр.',
        ]);

        $recipeWithFlour = $this->createRecipe('Баница');
        $recipeWithFlour->ingredients()->attach($flour->id, [
            'display_quantity' => 500,
            'display_unit' => 'г',
            'normalized_quantity' => 500,
        ]);

        $recipeWithEggs = $this->createRecipe('Омлет');
        $recipeWithEggs->ingredients()->attach($eggs->id, [
            'display_quantity' => 3,
            'display_unit' => 'бр.',
            'normalized_quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index', ['search' => 'Брашно']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->has('recipes.data', 1)
            ->where('recipes.data.0.title', 'Баница')
        );
    }

    public function test_search_with_partial_match(): void
    {
        $this->createRecipe('Домашна лютеница');
        $this->createRecipe('Мусака');

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index', ['search' => 'лютеница']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->has('recipes.data', 1)
            ->where('recipes.data.0.title', 'Домашна лютеница')
        );
    }

    public function test_search_returns_empty_when_no_match(): void
    {
        $this->createRecipe('Мусака');

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index', ['search' => 'Пица']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->has('recipes.data', 0)
        );
    }

    public function test_filters_prop_is_passed_back(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('recipes.index', ['search' => 'тест']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('filters.search', 'тест')
        );
    }

    private function createRecipe(string $title): Recipe
    {
        return Recipe::create([
            'user_id' => $this->user->id,
            'title' => $title,
            'description' => 'Тестово описание',
            'instructions' => 'Тестови инструкции',
            'prep_time' => 10,
            'cook_time' => 30,
            'servings' => 4,
        ]);
    }
}
