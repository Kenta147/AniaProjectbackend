<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Configuración general del sitio (singleton).
 *
 * Solo existe un registro. Si no existe, se crea al primer GET.
 * Las imágenes se guardan en storage/public/site.
 *
 * Rutas sugeridas:
 *  GET    /api/site-settings        → get
 *  POST   /api/site-settings        → update  (crea o actualiza el singleton)
 */
class SiteSettingController extends Controller
{
    // ──────────────────────────────────────────
    // GET — Obtener la configuración actual
    // ──────────────────────────────────────────
    public function get()
    {
        $settings = SiteSetting::firstOrCreate([]);

        return response()->json($settings);
    }

    // ──────────────────────────────────────────
    // UPDATE — Guardar configuración del sitio
    // ──────────────────────────────────────────
    public function update(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords'    => 'nullable|string|max:500',
            'author'      => 'nullable|string|max:255',
            'email'       => 'nullable|email',
            // Imágenes (campos opcionales de archivo)
            'bg_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'bg_image2'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'favicon'     => 'nullable|image|mimes:ico,png,jpg,svg|max:1024',
        ]);

        $settings = SiteSetting::firstOrCreate([]);

        // Procesar cada imagen de manera centralizada
        $imageFields = [
            'bg_image'  => 'bg_image_url',
            'bg_image2' => 'bg_image2_url',
            'logo'      => 'logo_url',
            'favicon'   => 'favicon_url',
        ];

        foreach ($imageFields as $fileKey => $dbColumn) {
            if ($request->hasFile($fileKey)) {
                $this->deleteStorageFile($settings->$dbColumn);
                $path = $request->file($fileKey)->store('site', 'public');
                $validated[$dbColumn] = Storage::url($path);
            }
            // Eliminar la clave de archivo de $validated (no es columna de BD)
            unset($validated[$fileKey]);
        }

        $settings->update($validated);

        return response()->json($settings);
    }

    // ──────────────────────────────────────────
    // Helper
    // ──────────────────────────────────────────
    private function deleteStorageFile(?string $url): void
    {
        if (!$url) return;
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
        Storage::disk('public')->delete($path);
    }
}
