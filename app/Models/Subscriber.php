<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Suscriptor de newsletter.
 *
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property bool   $is_active
 * @property \Carbon\Carbon $subscribed_at
 */
class Subscriber extends Model
{
    protected $fillable = [
        'name',
        'email',
        'is_active',
        'subscribed_at',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'subscribed_at' => 'datetime',
    ];
}
