<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * CRUD de Categorías.
 *
 * Rutas sugeridas:
 *  GET    /api/categories           → index
 *  POST   /api/categories           → store
 *  GET    /api/categories/{id}      → show
 *  POST   /api/categories/{id}      → update  (usa POST + _method=PUT para enviar archivos)
 *  DELETE /api/categories/{id}      → destroy
 */
class CategoryController extends Controller
{
    // ──────────────────────────────────────────
    // INDEX — Listar todas las categorías
    // ──────────────────────────────────────────
    public function index()
    {
        $categories = Category::withCount('clips')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    // ──────────────────────────────────────────
    // SHOW — Ver una categoría
    // ──────────────────────────────────────────
    public function show(Category $category)
    {
        $category->loadCount('clips');
        return response()->json($category);
    }

    // ──────────────────────────────────────────
    // STORE — Crear categoría (con imagen opcional)
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'is_active'   => 'nullable|boolean',
        ]);

        // Auto-generar slug si no viene
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        // Guardar imagen en storage/public/categories
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    // ──────────────────────────────────────────
    // UPDATE — Actualizar categoría
    // ──────────────────────────────────────────
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'slug'        => 'sometimes|nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'is_active'   => 'nullable|boolean',
        ]);

        // Reemplazar imagen: eliminar la anterior y subir la nueva
        if ($request->hasFile('image')) {
            $this->deleteStorageFile($category->image_url);
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $category->update($validated);

        return response()->json($category);
    }

    // ──────────────────────────────────────────
    // DESTROY — Eliminar categoría
    // ──────────────────────────────────────────
    public function destroy(Category $category)
    {
        $this->deleteStorageFile($category->image_url);
        $category->delete();

        return response()->json(['message' => 'Categoría eliminada.']);
    }

    // ──────────────────────────────────────────
    // Helper: eliminar archivo de storage
    // ──────────────────────────────────────────
    private function deleteStorageFile(?string $url): void
    {
        if (!$url) return;
        // Convertir URL pública a ruta de storage
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
        Storage::disk('public')->delete($path);
    }
}
