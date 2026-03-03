<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Red social / enlace externo del sitio.
 *
 * @property int    $id
 * @property string $name       Nombre legible (ej: "Instagram")
 * @property string $platform   Identificador en minúsculas (ej: "instagram")
 * @property string $url        URL del perfil
 * @property string $icon_url   Ícono almacenado en storage/public
 * @property bool   $is_active
 */
class Network extends Model
{
    // Laravel usa la tabla "networks" automáticamente por convención
    protected $table = 'networks';

    protected $fillable = [
        'name',
        'platform',
        'url',
        'icon_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
