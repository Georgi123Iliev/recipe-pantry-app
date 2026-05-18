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
    ) {
    }

    public function index()
    {
        $query = Recipe::with(['ingredients', 'images', 'user'])
            ->withCount('ratings')
            ->withAvg('ratings', 'value');


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
                ->orWhereHas(
                    'ingredients',
                    fn($q) =>
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
        $recipe->load(['ingredients', 'images', 'user'])
            ->loadCount('ratings')
            ->loadAvg('ratings', 'value');

        $user = auth()->user();
        $existingRating = $user
            ? $recipe->ratings()->where('user_id', $user->id)->first()
            : null;

        $paginatedRatings = $recipe->ratings()
            ->with('user')
            ->latest()
            ->paginate(2)
            ->withQueryString();

        return Inertia::render('Recipes/Show', [
            'recipe' => new RecipeResource($recipe),
            'ratings' => [
                'data' => $paginatedRatings->items() ? collect($paginatedRatings->items())->map(fn($rating) => [
                    'id' => $rating->id,
                    'value' => $rating->value,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                    'user' => [
                        'id' => $rating->user->id,
                        'name' => $rating->user->name,
                    ],
                ]) : [],
                'meta' => [
                    'current_page' => $paginatedRatings->currentPage(),
                    'last_page' => $paginatedRatings->lastPage(),
                    'per_page' => $paginatedRatings->perPage(),
                    'total' => $paginatedRatings->total(),
                    'links' => $paginatedRatings->linkCollection()->toArray(),
                ],
            ],
            'userRating' => $existingRating?->value,
            'userReview' => $existingRating?->review,
        ]);
    }

    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        $recipe->load(['ingredients', 'images']);

        return Inertia::render('Recipes/Edit', [
            'recipe' => new RecipeResource($recipe),
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
