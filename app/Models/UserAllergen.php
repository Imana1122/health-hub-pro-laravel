<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserAllergen extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'allergen_id', 'status'];

    protected $table = 'user_allergens';

    public function allergen()
    {
        return $this->belongsTo(Allergen::class, 'allergen_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
