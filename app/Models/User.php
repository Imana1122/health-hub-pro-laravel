<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

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
        'image'
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

    public function userMealLogs(){
        return $this->hasMany(UserRecipeLog::class,"user_id");
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

    public function dieticianBookings(){
        return $this->hasMany(DieticianBooking::class,'user_id');
    }


    public function sentMessages()
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(ChatMessage::class, 'receiver');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'user');
    }









}
