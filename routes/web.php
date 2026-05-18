<?php

use App\Http\Controllers\PantryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeController;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $latestRecipes = Recipe::with(['ingredients', 'images', 'user'])
        ->withCount('ratings')
        ->withAvg('ratings', 'value')
        ->latest()
        ->take(5)
        ->get();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'latestRecipes' => RecipeResource::collection($latestRecipes),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('recipes', RecipeController::class)->except(['index', 'show']);
    Route::post('/recipes/{recipe}/rate', [RatingController::class, 'store'])->name('recipes.rate');

    Route::get('/pantry', [PantryController::class, 'index'])->name('pantry.index');
    Route::post('/pantry', [PantryController::class, 'store'])->name('pantry.store');
    Route::delete('/pantry/{pantryItem}', [PantryController::class, 'destroy'])->name('pantry.destroy');
});

Route::resource('recipes', RecipeController::class)->only(['index', 'show']);

Route::get('/guest-prompt', function () {
    return Inertia::render('GuestPrompt', [
        'feature' => request()->query('feature', ''),
    ]);
})->name('guest.prompt');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'update', 'destroy']);
    Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class)->except(['show']);
});

require __DIR__.'/auth.php';
