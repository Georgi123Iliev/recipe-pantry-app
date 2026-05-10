<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('category')->orderBy('name')->get();
        $categories = IngredientCategory::orderBy('name')->get();

        return Inertia::render('Admin/Ingredients/Index', [
            'ingredients' => $ingredients,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name',
            'base_unit' => 'required|in:g,ml,бр.',
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
        ]);

        Ingredient::create($validated);

        return back();
    }

    public function create()
    {
        // Not used, handled in modal on index page
        return back();
    }

    public function edit(Ingredient $ingredient)
    {
        // Not used, handled in modal on index page
        return back();
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $ingredient->id,
            'base_unit' => 'required|in:g,ml,бр.',
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
        ]);

        $ingredient->update($validated);

        return back();
    }

    public function destroy(Ingredient $ingredient)
    {
        try {
            $ingredient->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraint violation
            if ($e->getCode() == 23000) {
                return back()->withErrors(['error' => 'Тази съставка не може да бъде изтрита, защото се използва в рецепти или килери.']);
            }
            throw $e;
        }

        return back();
    }
}
