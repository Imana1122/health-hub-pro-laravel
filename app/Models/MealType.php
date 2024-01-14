<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class MealType extends Model
{
    use HasFactory;
    protected $table ='meal_types';
    protected $fillable = ['name', 'slug','status'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'meal_type_id');
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


}
