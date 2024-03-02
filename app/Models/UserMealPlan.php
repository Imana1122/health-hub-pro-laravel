<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserMealPlan extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'breakfast',
        'lunch',
        'snack',
        'dinner',
        'calories',
        'carbohydrates',
        'protein',
        'total_fat',
        'saturated_fat',
        'sodium',
        'sugar',
    ];

    public function breakfastRecipe()
    {
        return $this->belongsTo(Recipe::class, 'breakfast');
    }

    public function lunchRecipe()
    {
        return $this->belongsTo(Recipe::class, 'lunch');
    }

    public function snackRecipe()
    {
        return $this->belongsTo(Recipe::class, 'snack');
    }

    public function dinnerRecipe()
    {
        return $this->belongsTo(Recipe::class, 'dinner');
    }
}
