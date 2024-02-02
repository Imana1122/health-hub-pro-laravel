<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllergenRecipe extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'allergen_id', 'status'];


    public function allergen()
    {
        return $this->belongsTo(Allergen::class, 'allergen_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }
}
