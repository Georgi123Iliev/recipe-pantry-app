<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'value'  => ['required', 'numeric', 'between:0.5,5', function ($attribute, $value, $fail) {
                if (fmod($value * 2, 1) !== 0.0) {
                    $fail('Оценката трябва да бъде кратна на 0.5.');
                }
            }],
            'review' => 'nullable|string|max:1000',
        ]);

        $recipe->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'value'  => $validated['value'],
                'review' => $validated['review'] ?? null,
            ]
        );

        return redirect()->back();
    }
}
