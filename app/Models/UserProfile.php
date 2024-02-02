<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory;
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
        'weight_plan_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function weightPlan()
    {
        return $this->belongsTo(WeightPlan::class);
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
