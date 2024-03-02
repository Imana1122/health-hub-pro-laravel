<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RecipeCategory extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = ['name', 'slug','status'];
    protected $table ='recipe_categories';
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }



}
