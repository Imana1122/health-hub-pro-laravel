<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HealthCondition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug','status'];
    public function healthConditionRecipe()
    {
        return $this->hasMany(HealthConditionRecipe::class, 'health_condition_id');
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
        return $this->belongsToMany(Recipe::class, 'health_condition_recipe', 'health_condition_id', 'recipe_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
