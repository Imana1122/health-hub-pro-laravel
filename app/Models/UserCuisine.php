<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserCuisine extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'cuisine_id', 'status'];
    protected $table = 'user_cuisines';


    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class, 'cuisine_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
