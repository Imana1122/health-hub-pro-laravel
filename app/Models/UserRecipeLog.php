<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserRecipeLog extends Model
{
    use HasFactory,HasUuids;


    protected $fillable = [
        'user_id',
        'recipe_id',
        'created_at',
        'updated_at'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
