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
            'suggestions' => $suggestions->map(fn ($r) => [
                'id'                    => $r->id,
                'title'                 => $r->title,
                'matched_count'         => $r->matched_count,
                'total_ingredient_count'=> $r->total_ingredient_count,
                'images'                => $r->images->map(fn ($img) => [
                    'id'  => $img->id,
                    'url' => \Storage::url($img->path),
                ]),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
        ]);

        $exists = auth()->user()->pantryItems()
            ->where('ingredient_id', $validated['ingredient_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['ingredient_id' => 'Тази съставка вече е в килера.']);
        }

        auth()->user()->pantryItems()->create([
            'ingredient_id' => $validated['ingredient_id'],
            'quantity'       => 0,
        ]);

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
