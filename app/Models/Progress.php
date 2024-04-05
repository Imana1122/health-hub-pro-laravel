<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;
    protected $table='progress';

    protected $fillable=[
        'front_image',
        'back_image',
        'left_image',
        'right_image',
        'weight',
        'height'
    ];
}
