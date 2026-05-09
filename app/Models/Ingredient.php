<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['ingredient_category_id', 'name', 'base_unit'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id');
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->withPivot('display_quantity', 'display_unit', 'normalized_quantity');
    }

    public function pantryItems(): HasMany
    {
        return $this->hasMany(PantryItem::class);
    }

    public function shoppingListItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
