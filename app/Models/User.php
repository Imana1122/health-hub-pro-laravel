<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
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

    public function workoutLogs(){
        return $this->hasMany(WorkoutLog::class);
    }
    public function userProfile(){
        return $this->hasOne(UserProfile::class);
    }
    public function userCuisines(){
        return $this->hasMany(UserCuisine::class,'user_id');
    }
    public function userAllergens(){
        return $this->hasMany(UserAllergen::class,'user_id');
    }
    public function userHealthConditions(){
        return $this->hasMany(UserHealthCondition::class);
    }

    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'user_allergens', 'user_id', 'allergen_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function healthConditions()
    {
        return $this->belongsToMany(HealthCondition::class, 'user_health_conditions', 'user_id', 'health_condition_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'user_cuisines', 'user_id', 'cuisine_id')
            ->withPivot('status')
            ->withTimestamps();
    }

}
