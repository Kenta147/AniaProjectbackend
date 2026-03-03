<?php

namespace App\Http\Controllers;

use App\Models\Clip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * CRUD de Clips (videos).
 *
 * Los archivos (video y miniatura) se guardan en storage/public.
 * Solo la URL se almacena en la base de datos.
 *
 * Gestión de estados:
 *  - draft      → al crear, por defecto
 *  - published  → PATCH /api/clips/{id}/publish
 *  - archived   → PATCH /api/clips/{id}/archive
 *
 * Rutas sugeridas:
 *  GET    /api/clips                    → index (permite ?status=published)
 *  POST   /api/clips                    → store
 *  GET    /api/clips/{id}               → show
 *  POST   /api/clips/{id}               → update
 *  DELETE /api/clips/{id}               → destroy
 *  PATCH  /api/clips/{id}/status        → changeStatus
 */
class ClipController extends Controller
{
    // ──────────────────────────────────────────
    // INDEX — Listar clips (filtro por estado)
    // ──────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Clip::with('category')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->paginate(15));
    }

    // ──────────────────────────────────────────
    // SHOW — Ver un clip
    // ──────────────────────────────────────────
    public function show(Clip $clip)
    {
        $clip->load('category');
        return response()->json($clip);
    }

    // ──────────────────────────────────────────
    // STORE — Crear clip con video y thumbnail
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'nullable|exists:categories,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'video'        => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:204800', // 200 MB
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'       => ['nullable', Rule::in(Clip::STATUSES)],
        ]);

        // Subir video al storage
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('clips/videos', 'public');
            $validated['video_url'] = Storage::url($path);
        }

        // Subir thumbnail al storage
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('clips/thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::url($path);
        }

        $clip = Clip::create($validated);
        $clip->load('category');

        return response()->json($clip, 201);
    }

    // ──────────────────────────────────────────
    // UPDATE — Actualizar clip
    // ──────────────────────────────────────────
    public function update(Request $request, Clip $clip)
    {
        $validated = $request->validate([
            'category_id'  => 'nullable|exists:categories,id',
            'title'        => 'sometimes|required|string|max:255',
            'description'  => 'nullable|string',
            'video'        => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:204800',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status'       => ['nullable', Rule::in(Clip::STATUSES)],
        ]);

        // Reemplazar video
        if ($request->hasFile('video')) {
            $this->deleteStorageFile($clip->video_url);
            $path = $request->file('video')->store('clips/videos', 'public');
            $validated['video_url'] = Storage::url($path);
        }

        // Reemplazar thumbnail
        if ($request->hasFile('thumbnail')) {
            $this->deleteStorageFile($clip->thumbnail_url);
            $path = $request->file('thumbnail')->store('clips/thumbnails', 'public');
            $validated['thumbnail_url'] = Storage::url($path);
        }

        $clip->update($validated);
        $clip->load('category');

        return response()->json($clip);
    }

    // ──────────────────────────────────────────
    // DESTROY — Eliminar clip y sus archivos
    // ──────────────────────────────────────────
    public function destroy(Clip $clip)
    {
        $this->deleteStorageFile($clip->video_url);
        $this->deleteStorageFile($clip->thumbnail_url);
        $clip->delete();

        return response()->json(['message' => 'Clip eliminado.']);
    }

    // ──────────────────────────────────────────
    // CHANGE STATUS — Cambiar estado del clip
    // ──────────────────────────────────────────
    public function changeStatus(Request $request, Clip $clip)
    {
        $request->validate([
            'status' => ['required', Rule::in(Clip::STATUSES)],
        ]);

        $clip->update(['status' => $request->status]);

        return response()->json([
            'message' => "Estado cambiado a '{$request->status}'.",
            'clip'    => $clip,
        ]);
    }

    // ──────────────────────────────────────────
    // Helper: eliminar archivo de storage
    // ──────────────────────────────────────────
    private function deleteStorageFile(?string $url): void
    {
        if (!$url) return;
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
        Storage::disk('public')->delete($path);
    }
}
