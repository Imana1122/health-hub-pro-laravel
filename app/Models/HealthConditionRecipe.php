<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HealthConditionRecipe extends Model
{
    use HasFactory;
    protected $fillable = ['recipe_id', 'health_condition_id', 'status'];


    public function healthCondition()
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }
}
