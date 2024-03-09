<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMealPlan extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = [
        'user_id',
        'meal_plan_id',
        'calories',
        'carbohydrates',
        'protein',
        'total_fat',
        'sodium',
        'sugar',
        'created_at',
        'updated_at'
    ];
}
