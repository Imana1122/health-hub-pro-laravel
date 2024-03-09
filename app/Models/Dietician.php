<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Dietician extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable,HasUuids;

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
        'esewa_client_id',
        'esewa_secret_key',
        'booking_amount',
        'password',
        'bio',
        'status'
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


    public function sentMessages()
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(ChatMessage::class, 'receiver');
    }

    public function messages()
    {
        // Get all messages where the current model is the sender
        $sentMessages = $this->sentMessages();

        // Get all messages where the current model is the receiver
        $receivedMessages = $this->receivedMessages();

        // Use the `union` method to combine both queries
        return $sentMessages->union($receivedMessages)->orderBy('created_at', 'desc');
    }



}
