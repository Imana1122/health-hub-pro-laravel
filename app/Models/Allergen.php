<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Allergen extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'slug','status'];
    protected $table ='allergens';
    public function allergenRecipes()
    {
        return $this->hasMany(AllergenRecipe::class, 'allergen_id');
    }



    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'allergen_recipes', 'allergen_id', 'recipe_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
