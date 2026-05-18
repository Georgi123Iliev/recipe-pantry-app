<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeRatingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Recipe $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $category = IngredientCategory::create(['name' => 'Основни']);

        $flour = Ingredient::create([
            'ingredient_category_id' => $category->id,
            'name' => 'Брашно',
            'base_unit' => 'g',
        ]);

        $this->recipe = Recipe::create([
            'user_id' => $this->user->id,
            'title' => 'Тестова рецепта',
            'description' => 'Тестово описание',
            'instructions' => 'Тестови инструкции',
            'prep_time' => 10,
            'cook_time' => 30,
            'servings' => 4,
        ]);

        $this->recipe->ingredients()->attach($flour->id, [
            'display_quantity' => 500,
            'display_unit' => 'g',
            'normalized_quantity' => 500,
        ]);
    }

    public function test_guest_cannot_rate_recipe(): void
    {
        $response = $this->post(route('recipes.rate', $this->recipe), [
            'value' => 4,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_authenticated_user_can_rate_recipe(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 4,
                'review' => 'Чудесна рецепта!',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 4,
            'review' => 'Чудесна рецепта!',
        ]);
    }

    public function test_rating_value_must_be_between_1_and_5(): void
    {
        $responseTooLow = $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 0,
            ]);

        $responseTooLow->assertSessionHasErrors('value');

        $responseTooHigh = $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 6,
            ]);

        $responseTooHigh->assertSessionHasErrors('value');

        $this->assertDatabaseCount('ratings', 0);
    }

    public function test_review_is_optional(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 3,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 3,
            'review' => null,
        ]);
    }

    public function test_review_max_length_validated(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 4,
                'review' => str_repeat('а', 1001),
            ]);

        $response->assertSessionHasErrors('review');
    }

    public function test_user_can_update_existing_rating(): void
    {
        // First rating
        $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 3,
                'review' => 'Добра.',
            ]);

        // Update rating
        $this->actingAs($this->user)
            ->post(route('recipes.rate', $this->recipe), [
                'value' => 5,
                'review' => 'Всъщност е страхотна!',
            ]);

        $this->assertDatabaseCount('ratings', 1);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 5,
            'review' => 'Всъщност е страхотна!',
        ]);
    }

    public function test_recipe_average_rating_calculated_correctly(): void
    {
        Rating::create([
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 3,
        ]);

        Rating::create([
            'user_id' => $this->otherUser->id,
            'recipe_id' => $this->recipe->id,
            'value' => 5,
        ]);

        $avg = $this->recipe->averageRating();
        $count = $this->recipe->ratingsCount();

        $this->assertEquals(4.0, $avg);
        $this->assertEquals(2, $count);
    }

    public function test_index_includes_rating_data(): void
    {
        Rating::create([
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 4,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('recipes.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Index')
            ->where('recipes.data.0.ratings_avg', fn ($val) => (float) $val === 4.0)
            ->where('recipes.data.0.ratings_count', 1)
        );
    }

    public function test_show_includes_user_rating_and_reviews(): void
    {
        Rating::create([
            'user_id' => $this->user->id,
            'recipe_id' => $this->recipe->id,
            'value' => 4,
            'review' => 'Много вкусно!',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('recipes.show', $this->recipe));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Recipes/Show')
            ->where('userRating', 4)
            ->where('userReview', 'Много вкусно!')
            ->has('ratings.data', 1)
            ->where('ratings.meta.total', 1)
        );
    }
}
