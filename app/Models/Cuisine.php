<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Cuisine extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug','status'];
    protected $table = 'cuisines';
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'cuisine_id');
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

