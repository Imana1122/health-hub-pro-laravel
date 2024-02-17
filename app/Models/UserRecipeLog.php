<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserRecipeLog extends Model
{
    use HasFactory;
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
