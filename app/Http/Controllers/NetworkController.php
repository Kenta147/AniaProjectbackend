<?php

namespace App\Http\Controllers;

use App\Models\Network;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD de Redes Sociales.
 *
 * Rutas sugeridas:
 *  GET    /api/networks           → index
 *  POST   /api/networks           → store
 *  GET    /api/networks/{id}      → show
 *  POST   /api/networks/{id}      → update
 *  DELETE /api/networks/{id}      → destroy
 */
class NetworkController extends Controller
{
    // ──────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────
    public function index()
    {
        return response()->json(Network::orderBy('name')->get());
    }

    // ──────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────
    public function show(Network $network)
    {
        return response()->json($network);
    }

    // ──────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'platform'  => 'required|string|max:100',
            'url'       => 'required|url',
            'icon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('networks', 'public');
            $validated['icon_url'] = Storage::url($path);
        }

        $network = Network::create($validated);

        return response()->json($network, 201);
    }

    // ──────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────
    public function update(Request $request, Network $network)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'platform'  => 'sometimes|required|string|max:100',
            'url'       => 'sometimes|required|url',
            'icon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('icon')) {
            $this->deleteStorageFile($network->icon_url);
            $path = $request->file('icon')->store('networks', 'public');
            $validated['icon_url'] = Storage::url($path);
        }

        $network->update($validated);

        return response()->json($network);
    }

    // ──────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────
    public function destroy(Network $network)
    {
        $this->deleteStorageFile($network->icon_url);
        $network->delete();

        return response()->json(['message' => 'Red social eliminada.']);
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
