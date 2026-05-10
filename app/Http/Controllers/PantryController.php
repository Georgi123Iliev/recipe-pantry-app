<?php

namespace App\Http\Controllers;

use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Services\PantryAlgorithmService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PantryController extends Controller
{
    public function __construct(
        private PantryAlgorithmService $algorithmService,
    ) {}

    public function index()
    {
        $user = auth()->user();

        $pantryItems = $user->pantryItems()
            ->with('ingredient.category')
            ->get();

        $suggestions = $this->algorithmService->getResults($user);

        return Inertia::render('Pantry/Index', [
            'pantry_items' => $pantryItems,
            'ingredients' => IngredientResource::collection(
                Ingredient::with('category')->orderBy('name')->get()
            ),
            'suggestions' => [
                'perfect' => $suggestions['perfect']->map(fn ($r) => [
                    'id' => $r->id,
                    'title' => $r->title,
                    'images' => $r->images->map(fn ($img) => ['id' => $img->id, 'url' => \Storage::url($img->path)]),
                ]),
                'partial' => $suggestions['partial']->map(fn ($r) => [
                    'id' => $r->id,
                    'title' => $r->title,
                    'missing_ingredients_count' => $r->missing_ingredients_count,
                    'images' => $r->images->map(fn ($img) => ['id' => $img->id, 'url' => \Storage::url($img->path)]),
                ]),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $existing = auth()->user()->pantryItems()
            ->where('ingredient_id', $validated['ingredient_id'])
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $validated['quantity'],
            ]);
        } else {
            auth()->user()->pantryItems()->create($validated);
        }

        return redirect()->back();
    }

    public function update(Request $request, PantryItem $pantryItem)
    {
        if ($pantryItem->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        if ($validated['quantity'] <= 0) {
            $pantryItem->delete();
        } else {
            $pantryItem->update($validated);
        }

        return redirect()->back();
    }

    public function destroy(PantryItem $pantryItem)
    {
        if ($pantryItem->user_id !== auth()->id()) {
            abort(403);
        }

        $pantryItem->delete();

        return redirect()->back();
    }
}
