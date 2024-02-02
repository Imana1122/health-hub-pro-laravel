<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;
    protected $primaryKey = 'phone_number';
    protected $fillable = [
        'phone_number',
        'code',
        'expires_at'
    ];

    protected $hidden = [
        'code' => 'hashed',
    ];
}
