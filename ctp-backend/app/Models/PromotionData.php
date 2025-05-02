<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionData extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'description',
    ];

    public $timestamps = true;
}
