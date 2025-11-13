<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'category',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];
}
