<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Clip de video del sitio.
 *
 * Estados:
 *  - draft      → borrador, solo visible para admins
 *  - published  → publicado, visible para todos
 *  - archived   → archivado, oculto del catálogo
 *
 * @property int    $id
 * @property int    $category_id
 * @property string $title
 * @property string $description
 * @property string $video_url        URL del video almacenado en storage/public
 * @property string $thumbnail_url    URL de la miniatura en storage/public
 * @property string $status           draft|published|archived
 */
class Clip extends Model
{
    use HasFactory;

    // ──────────────────────────────────────────
    // Constantes de estado
    // ──────────────────────────────────────────

    const STATUS_DRAFT     = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED  = 'archived';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'status',
    ];

    protected $casts = [
        'category_id' => 'integer',
    ];

    // ──────────────────────────────────────────
    // Relaciones
    // ──────────────────────────────────────────

    /** Un clip pertenece a una categoría */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
