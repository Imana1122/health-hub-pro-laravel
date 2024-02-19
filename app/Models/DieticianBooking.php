<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DieticianBooking extends Model
{
    use HasFactory;
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
