<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecipeResource;
use App\Services\PantryAlgorithmService;
use Inertia\Inertia;

class PantryAlgorithmController extends Controller
{
    public function __construct(
        private PantryAlgorithmService $algorithmService,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $results = $this->algorithmService->getResults($user);

        return Inertia::render('Pantry/Suggestions', [
            'perfect' => RecipeResource::collection($results['perfect']),
            'partial' => RecipeResource::collection($results['partial']),
        ]);
    }
}
