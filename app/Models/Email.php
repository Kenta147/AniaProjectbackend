<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Plantilla de email reutilizable.
 *
 * Permite al admin guardar plantillas HTML con imagen de cabecera
 * y enviarlas a los suscriptores desde el panel.
 *
 * @property int    $id
 * @property string $name        Etiqueta interna (ej: "Newsletter Mayo")
 * @property string $subject     Asunto del correo
 * @property string $body        Cuerpo HTML del correo
 * @property string $image_url   Imagen de cabecera almacenada en storage/public
 */
class Email extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body',
        'image_url',
    ];
}
