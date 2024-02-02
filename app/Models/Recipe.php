<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Recipe extends Model
{
    use HasFactory;
    protected $table ='recipes';
    protected $fillable = [
        'title',
        'slug',
        'minutes',
        'description',
        'tags',
        'steps',
        'cuisine_id',
        'meal_type_id',
        'calories',
        'carbohydrates',
        'protein',
        'total_fat',
        'saturated_fat',
        'sodium',
        'sugar',
        'status'
    ];

    protected $casts = [
        'steps' => 'json',
        'tags' => 'json',
        'ingredients'=> 'json'
    ];

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

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class, 'cuisine_id');
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function meal_type()
    {
        return $this->belongsTo(MealType::class, 'meal_type_id');
    }

    public function allergenRecipes()
    {
        return $this->hasMany(AllergenRecipe::class, 'recipe_id'); // Optional
    }

    public function healthConditionRecipes()
    {
        return $this->hasMany(HealthConditionRecipe::class, 'recipe_id'); // Optional
    }

    public function images()
    {
        return $this->hasMany(RecipeImage::class, 'recipe_id');
    }
    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class, 'recipe_id'); // Optional
    }
    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'allergen_recipes', 'recipe_id', 'allergen_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function healthConditions()
    {
        return $this->belongsToMany(HealthCondition::class, 'health_condition_recipes', 'recipe_id', 'health_condition_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function ingredient()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients', 'recipe_id', 'ingredient_id');

    }
}
