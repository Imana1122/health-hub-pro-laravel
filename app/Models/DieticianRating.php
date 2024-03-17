<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DieticianRating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'dietician_id', 'status','comment','rating'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dietician()
    {
        return $this->belongsTo(Dietician::class, 'dietician_id');
    }}
