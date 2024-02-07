<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;


class Dietician extends Model
{
    use HasFactory,HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'image',
        'cv',
        'speciality',
        'description',
        'approved_status',
        'availability_status',
        'esewa_id',
        'booking_amount',
        'password',
        'bio',
        'status'
    ];
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
