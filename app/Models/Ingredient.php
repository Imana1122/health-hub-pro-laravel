<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug'];
    protected $table ='ingredients';
    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class, 'ingredient_id');
    }

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid();
        });
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients', 'ingredient_id', 'recipe_id')
            ->withTimestamps();
    }
}
