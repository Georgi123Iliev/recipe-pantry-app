<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PantryItem extends Model
{
    protected $fillable = ['user_id', 'ingredient_id', 'quantity'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
