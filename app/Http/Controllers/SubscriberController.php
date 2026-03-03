<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * CRUD de Suscriptores.
 *
 * Rutas sugeridas:
 *  GET    /api/subscribers           → index
 *  POST   /api/subscribers           → store  (suscripción pública)
 *  GET    /api/subscribers/{id}      → show
 *  PUT    /api/subscribers/{id}      → update
 *  DELETE /api/subscribers/{id}      → destroy
 *  PATCH  /api/subscribers/{id}/toggle → toggle activo/inactivo
 */
class SubscriberController extends Controller
{
    // ──────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Subscriber::orderByDesc('created_at');

        // Filtrar por estado activo
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->get());
    }

    // ──────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────
    public function show(Subscriber $subscriber)
    {
        return response()->json($subscriber);
    }

    // ──────────────────────────────────────────
    // STORE — Suscripción pública (sin auth)
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'nullable|string|max:255',
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $validated['subscribed_at'] = Carbon::now();

        $subscriber = Subscriber::create($validated);

        return response()->json([
            'message'    => '¡Suscripción exitosa!',
            'subscriber' => $subscriber,
        ], 201);
    }

    // ──────────────────────────────────────────
    // UPDATE — Editar datos del suscriptor
    // ──────────────────────────────────────────
    public function update(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'name'      => 'nullable|string|max:255',
            'email'     => 'sometimes|required|email|unique:subscribers,email,' . $subscriber->id,
            'is_active' => 'nullable|boolean',
        ]);

        $subscriber->update($validated);

        return response()->json($subscriber);
    }

    // ──────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────
    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return response()->json(['message' => 'Suscriptor eliminado.']);
    }

    // ──────────────────────────────────────────
    // TOGGLE — Activar/desactivar suscriptor
    // ──────────────────────────────────────────
    public function toggle(Subscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);

        $state = $subscriber->is_active ? 'activado' : 'desactivado';

        return response()->json([
            'message'    => "Suscriptor {$state}.",
            'subscriber' => $subscriber,
        ]);
    }
}
