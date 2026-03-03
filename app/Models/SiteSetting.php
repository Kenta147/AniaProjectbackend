<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuración general del sitio (singleton — solo hay 1 fila).
 *
 * Las URLs de imágenes apuntan a archivos almacenados en storage/public.
 *
 * @property int    $id
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $author
 * @property string $email
 * @property string $bg_image_url    Imagen de fondo principal
 * @property string $bg_image2_url   Imagen de fondo secundaria
 * @property string $logo_url
 * @property string $favicon_url
 */
class SiteSetting extends Model
{
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'author',
        'email',
        'bg_image_url',
        'bg_image2_url',
        'logo_url',
        'favicon_url',
    ];
}
