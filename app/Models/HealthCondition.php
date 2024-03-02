<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HealthCondition extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = ['name', 'slug','status'];
    public function healthConditionRecipe()
    {
        return $this->hasMany(HealthConditionRecipe::class, 'health_condition_id');
    }



    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'health_condition_recipe', 'health_condition_id', 'recipe_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
