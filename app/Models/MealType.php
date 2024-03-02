<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class MealType extends Model
{
    use HasFactory,HasUuids;
    protected $table ='meal_types';
    protected $fillable = ['name', 'slug','status'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'meal_type_id');
    }



}
