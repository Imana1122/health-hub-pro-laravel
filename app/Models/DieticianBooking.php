<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DieticianBooking extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = ['user_id', 'dietician_id', 'payment_status','total_amount'];

    public function dietician()
    {
        return $this->belongsTo(Dietician::class, 'dietician_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
