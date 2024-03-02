<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = [
        'height',
        'weight',
        'waist',
        'hips',
        'bust',
        'targeted_weight',
        'age',
        'gender',
        'user_id',
        'weight_plan_id',
        'activity_level'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function weightPlan()
    {
        return $this->belongsTo(WeightPlan::class);
    }

}
