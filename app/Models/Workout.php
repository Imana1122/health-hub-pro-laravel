<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workout extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = ['name', 'slug','description', 'exercises', 'status','duration','image'];
    protected $casts = [
        'exercises' => 'json',
    ];


    public function workoutLogs(){
        return $this->hasMany(WorkoutLog::class);
    }
}
