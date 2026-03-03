<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Networks extends Model
{
    protected $fillable = [
        'url',
        'image_url',
        'is_active',
    ];
}
