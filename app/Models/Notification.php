<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory,HasUuids;
    protected $fillable= [
        'user_id',
        'user_type',
        'message',
        'scheduled_at',
        'image',
        'read'
    ];

    public function user()
    {
        return $this->morphTo();
    }
}
