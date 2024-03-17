<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutSchedule extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = [
        "user_id",
        'workout_id',
        'workout_type',
        'notifiable',
        'scheduled_time',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function workout()
    {
        return $this->morphTo();
    }
}
