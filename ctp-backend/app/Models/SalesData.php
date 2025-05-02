<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesData extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'hour',
        'total_sales',
    ];

    public $timestamps = true;
}
