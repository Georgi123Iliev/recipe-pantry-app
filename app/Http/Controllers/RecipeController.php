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


    //Мързеше ме да рекомпилирам sqliite, за да може кирилицата, затова ниписах това
    if (request()->filled('search')) {
        $search = request()->search;
        
        $exact = '%' . $search . '%';
        $lower = '%' . mb_strtolower($search, 'UTF-8') . '%';
        $upper = '%' . mb_strtoupper($search, 'UTF-8') . '%';
        $title = '%' . mb_convert_case($search, MB_CASE_TITLE, 'UTF-8') . '%';

        $query->where('title', 'like', $exact)
            ->orWhere('title', 'like', $lower)
            ->orWhere('title', 'like', $upper)
            ->orWhere('title', 'like', $title)
            ->orWhereHas('ingredients', fn ($q) => 
                $q->where('name', 'like', $exact)
                  ->orWhere('name', 'like', $lower)
                  ->orWhere('name', 'like', $upper)
                  ->orWhere('name', 'like', $title)
            );
    }

    $recipes = $query->latest()->paginate(8)->withQueryString();

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
