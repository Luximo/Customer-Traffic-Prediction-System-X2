<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualOverride extends Model
{
    protected $fillable = ['date', 'hour', 'value'];
}
