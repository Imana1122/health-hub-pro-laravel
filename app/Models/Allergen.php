<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Allergen extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug','status'];
    protected $table ='allergens';
    // public function RecipeForAllergens()
    // {
    //     return $this->hasMany(RecipeForAllergens::class, 'allergen_id');
    // }

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
}
