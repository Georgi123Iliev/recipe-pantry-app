<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('display_quantity', 'display_unit', 'normalized_quantity');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating(): ?float
    {
        return $this->ratings()->avg('value');
    }

    public function ratingsCount(): int
    {
        return $this->ratings()->count();
    }
}
