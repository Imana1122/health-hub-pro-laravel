<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WeightPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'status'
    ];
    public function userProfiles(){
        return $this->hasMany(UserProfile::class);
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
