<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\IngredientResource;
use App\Http\Resources\RecipeResource;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private RecipeService $recipeService,
    ) {}

    public function index()
    {
        $query = Recipe::with(['ingredients', 'images', 'user']);

        if (request()->filled('search')) {
            $searchTerm = '%' . request()->search . '%';
            $query->where('title', 'like', $searchTerm)
                ->orWhereHas('ingredients', fn ($q) => $q->where('name', 'like', $searchTerm));
        }

        $recipes = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Recipes/Index', [
            'recipes' => RecipeResource::collection($recipes),
            'filters' => request()->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Recipes/Create', [
            'ingredients' => IngredientResource::collection(
                Ingredient::with('category')->orderBy('name')->get()
            ),
        ]);
    }

    public function store(StoreRecipeRequest $request)
    {
        $this->recipeService->store($request->validated(), $request->user());

        return redirect()->route('recipes.index');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['ingredients', 'images', 'user']);

        return Inertia::render('Recipes/Show', [
            'recipe' => new RecipeResource($recipe),
        ]);
    }

    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->load(['ingredients', 'images']);

        return Inertia::render('Recipes/Edit', [
            'recipe'      => new RecipeResource($recipe),
            'ingredients' => IngredientResource::collection(
                Ingredient::with('category')->orderBy('name')->get()
            ),
        ]);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $this->recipeService->update($request->validated(), $recipe);

        return redirect()->route('recipes.index');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorize('delete', $recipe);

        $this->recipeService->delete($recipe);

        return redirect()->route('recipes.index');
    }
}
