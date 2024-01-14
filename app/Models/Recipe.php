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
        'description',
        'tags',
        'cuisine_id',
        'meal_type_id',
        'calories',
        'total_fat',
        'saturated_fat',
        'sodium',
        'status'
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

    public function meal_type()
    {
        return $this->belongsTo(MealType::class, 'meal_type_id');
    }

    public function allergens()
    {
        return $this->belongsTo(Allergen::class, 'allergen_id')->withDefault(); // Optional
    }

    public function images()
    {
        return $this->hasMany(RecipeImage::class, 'recipe_id');
    }
}
