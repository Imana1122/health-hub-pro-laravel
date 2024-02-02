<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workout extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug','description', 'exercises', 'status','duration','image'];
    protected $casts = [
        'exercises' => 'json',
    ];

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

    public function workoutLogs(){
        return $this->hasMany(WorkoutLog::class);
    }
}
