<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WeightPlan extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'protein_ratio',
        'carb_ratio',
        'fat_ratio',

        'status'
    ];
    public function userProfiles(){
        return $this->hasMany(UserProfile::class);
    }

}
