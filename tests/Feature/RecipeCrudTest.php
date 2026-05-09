<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private IngredientCategory $category;
    private Ingredient $flour;
    private Ingredient $eggs;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
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
    }

    public function test_create_page_loads_with_ingredients(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('recipes.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Create')
            ->has('ingredients.data', 2)
        );
    }

    public function test_store_creates_recipe_with_ingredients(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('recipes.store'), [
                'title' => 'Тестова рецепта',
                'description' => 'Описание',
                'instructions' => 'Стъпки за приготвяне',
                'prep_time' => 10,
                'cook_time' => 30,
                'servings' => 4,
                'ingredients' => [
                    [
                        'ingredient_id' => $this->flour->id,
                        'display_quantity' => 500,
                        'display_unit' => 'g',
                    ],
                    [
                        'ingredient_id' => $this->eggs->id,
                        'display_quantity' => 3,
                        'display_unit' => 'бр.',
                    ],
                ],
            ]);

        $response->assertRedirect(route('recipes.index'));

        $this->assertDatabaseHas('recipes', [
            'title' => 'Тестова рецепта',
            'user_id' => $this->user->id,
        ]);

        $recipe = Recipe::where('title', 'Тестова рецепта')->first();
        $this->assertCount(2, $recipe->ingredients);
    }

    public function test_store_creates_recipe_with_images(): void
    {
        $image = UploadedFile::fake()->create('recipe.png', 100, 'image/png');

        $response = $this->actingAs($this->user)
            ->post(route('recipes.store'), [
                'title' => 'Рецепта с картинка',
                'instructions' => 'Инструкции',
                'prep_time' => 5,
                'cook_time' => 15,
                'servings' => 2,
                'ingredients' => [
                    [
                        'ingredient_id' => $this->flour->id,
                        'display_quantity' => 200,
                        'display_unit' => 'g',
                    ],
                ],
                'images' => [$image],
            ]);

        $response->assertRedirect(route('recipes.index'));

        $recipe = Recipe::where('title', 'Рецепта с картинка')->first();
        $this->assertCount(1, $recipe->images);
        Storage::disk('public')->assertExists($recipe->images->first()->path);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('recipes.store'), []);

        $response->assertSessionHasErrors([
            'title', 'instructions', 'prep_time', 'cook_time', 'servings', 'ingredients',
        ]);
    }

    public function test_show_displays_recipe(): void
    {
        $recipe = $this->createRecipe('Мусака', $this->user);

        $response = $this->actingAs($this->user)
            ->get(route('recipes.show', $recipe));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Show')
            ->where('recipe.data.title', 'Мусака')
        );
    }

    public function test_edit_page_loads_for_owner(): void
    {
        $recipe = $this->createRecipe('Баница', $this->user);

        $response = $this->actingAs($this->user)
            ->get(route('recipes.edit', $recipe));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Edit')
            ->where('recipe.data.title', 'Баница')
            ->has('ingredients.data', 2)
        );
    }

    public function test_edit_page_denied_for_non_owner(): void
    {
        $recipe = $this->createRecipe('Баница', $this->user);

        $response = $this->actingAs($this->otherUser)
            ->get(route('recipes.edit', $recipe));

        $response->assertForbidden();
    }

    public function test_update_modifies_recipe(): void
    {
        $recipe = $this->createRecipe('Стара рецепта', $this->user);

        $response = $this->actingAs($this->user)
            ->put(route('recipes.update', $recipe), [
                'title' => 'Обновена рецепта',
                'instructions' => 'Нови инструкции',
                'prep_time' => 20,
                'cook_time' => 40,
                'servings' => 6,
                'ingredients' => [
                    [
                        'ingredient_id' => $this->eggs->id,
                        'display_quantity' => 5,
                        'display_unit' => 'бр.',
                    ],
                ],
            ]);

        $response->assertRedirect(route('recipes.index'));

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Обновена рецепта',
        ]);
    }

    public function test_update_denied_for_non_owner(): void
    {
        $recipe = $this->createRecipe('Рецепта', $this->user);

        $response = $this->actingAs($this->otherUser)
            ->put(route('recipes.update', $recipe), [
                'title' => 'Хакната рецепта',
                'instructions' => 'Хак',
                'prep_time' => 1,
                'cook_time' => 1,
                'servings' => 1,
                'ingredients' => [
                    [
                        'ingredient_id' => $this->flour->id,
                        'display_quantity' => 100,
                        'display_unit' => 'g',
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_destroy_deletes_recipe_for_owner(): void
    {
        $recipe = $this->createRecipe('За изтриване', $this->user);

        $response = $this->actingAs($this->user)
            ->delete(route('recipes.destroy', $recipe));

        $response->assertRedirect(route('recipes.index'));
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
    }

    public function test_destroy_denied_for_non_owner(): void
    {
        $recipe = $this->createRecipe('Защитена', $this->user);

        $response = $this->actingAs($this->otherUser)
            ->delete(route('recipes.destroy', $recipe));

        $response->assertForbidden();
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id]);
    }

    public function test_admin_can_edit_any_recipe(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $recipe = $this->createRecipe('Чужда рецепта', $this->user);

        $response = $this->actingAs($admin)
            ->get(route('recipes.edit', $recipe));

        $response->assertOk();
    }

    private function createRecipe(string $title, User $user): Recipe
    {
        $recipe = Recipe::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => 'Тестово описание',
            'instructions' => 'Тестови инструкции',
            'prep_time' => 10,
            'cook_time' => 30,
            'servings' => 4,
        ]);

        $recipe->ingredients()->attach($this->flour->id, [
            'display_quantity' => 500,
            'display_unit' => 'g',
            'normalized_quantity' => 500,
        ]);

        return $recipe;
    }
}
