<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class RecipeImage extends Model
{
    use HasFactory,HasUuids;
    protected $table ='recipe_images';
    protected $fillable = [
        'recipe_id',
        'image',
        'sort_order',
    ];


    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }


}
