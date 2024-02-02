<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserHealthCondition extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'health_condition_id', 'status'];
    protected $table = 'user_health_conditions';


    public function healthCondition()
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
