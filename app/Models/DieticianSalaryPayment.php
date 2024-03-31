<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DieticianSalaryPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'dietician_id',
        'year',
        'month',
        'amount',

    ];
}
