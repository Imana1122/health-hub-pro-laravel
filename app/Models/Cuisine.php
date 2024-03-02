<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Cuisine extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'slug','status'];
    protected $table = 'cuisines';
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'cuisine_id');
    }

}

