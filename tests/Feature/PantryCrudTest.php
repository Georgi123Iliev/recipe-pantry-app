<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\PantryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PantryCrudTest extends TestCase
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

    public function test_pantry_index_loads(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('pantry.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pantry/Index')
            ->has('pantry_items')
            ->has('ingredients')
            ->has('suggestions')
        );
    }

    public function test_store_adds_item_to_pantry(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('pantry.store'), [
                'ingredient_id' => $this->flour->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pantry_items', [
            'user_id' => $this->user->id,
            'ingredient_id' => $this->flour->id,
        ]);
    }

    public function test_store_rejects_duplicates(): void
    {
        PantryItem::create([
            'user_id' => $this->user->id,
            'ingredient_id' => $this->flour->id,
            'quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('pantry.store'), [
                'ingredient_id' => $this->flour->id,
            ]);

        $response->assertSessionHasErrors('ingredient_id');

        $this->assertEquals(1, PantryItem::where('user_id', $this->user->id)
            ->where('ingredient_id', $this->flour->id)
            ->count());
    }

    public function test_destroy_removes_item(): void
    {
        $item = PantryItem::create([
            'user_id' => $this->user->id,
            'ingredient_id' => $this->flour->id,
            'quantity' => 0,
        ]);

        $this->actingAs($this->user)
            ->delete(route('pantry.destroy', $item));

        $this->assertDatabaseMissing('pantry_items', ['id' => $item->id]);
    }

    public function test_destroy_denied_for_non_owner(): void
    {
        $item = PantryItem::create([
            'user_id' => $this->user->id,
            'ingredient_id' => $this->flour->id,
            'quantity' => 0,
        ]);

        $response = $this->actingAs($this->otherUser)
            ->delete(route('pantry.destroy', $item));

        $response->assertForbidden();
        $this->assertDatabaseHas('pantry_items', ['id' => $item->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('pantry.store'), []);

        $response->assertSessionHasErrors(['ingredient_id']);
    }
}
