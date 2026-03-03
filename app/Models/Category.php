<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Categoría de clips/videos del sitio.
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $image_url
 * @property bool   $is_active
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ──────────────────────────────────────────
    // Relaciones
    // ──────────────────────────────────────────

    /** Un category tiene muchos clips */
    public function clips()
    {
        return $this->hasMany(Clip::class);
    }
}
