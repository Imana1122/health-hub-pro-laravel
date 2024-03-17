<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkoutLog extends Model
{
    use HasFactory;
    protected $fillable = [
        "start_at",
        'end_at',
        'calories_burned',
        'completion_status',
        'user_id',
        'workout_name',
        'workout_id',
        'workout_type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function workout()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'exercises' => 'json',
    ];
}
