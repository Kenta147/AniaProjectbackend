<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Subscriber;
use App\Mail\EmailTemplateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD de Plantillas de Email + envío masivo a suscriptores.
 *
 * Rutas sugeridas:
 *  GET    /api/emails              → index
 *  POST   /api/emails              → store
 *  GET    /api/emails/{id}         → show
 *  POST   /api/emails/{id}         → update
 *  DELETE /api/emails/{id}         → destroy
 *  POST   /api/emails/{id}/send    → send  (envía a todos los suscriptores activos)
 */
class EmailController extends Controller
{
    // ──────────────────────────────────────────
    // INDEX — Listar plantillas
    // ──────────────────────────────────────────
    public function index()
    {
        return response()->json(Email::orderByDesc('created_at')->get());
    }

    // ──────────────────────────────────────────
    // SHOW — Ver una plantilla
    // ──────────────────────────────────────────
    public function show(Email $email)
    {
        return response()->json($email);
    }

    // ──────────────────────────────────────────
    // STORE — Crear plantilla (con imagen opcional)
    // ──────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('emails', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $email = Email::create($validated);

        return response()->json($email, 201);
    }

    // ──────────────────────────────────────────
    // UPDATE — Actualizar plantilla
    // ──────────────────────────────────────────
    public function update(Request $request, Email $email)
    {
        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
            'body'    => 'sometimes|required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStorageFile($email->image_url);
            $path = $request->file('image')->store('emails', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $email->update($validated);

        return response()->json($email);
    }

    // ──────────────────────────────────────────
    // DESTROY — Eliminar plantilla
    // ──────────────────────────────────────────
    public function destroy(Email $email)
    {
        $this->deleteStorageFile($email->image_url);
        $email->delete();

        return response()->json(['message' => 'Plantilla eliminada.']);
    }

    // ──────────────────────────────────────────
    // SEND — Enviar esta plantilla a suscriptores activos
    // ──────────────────────────────────────────
    public function send(Email $email)
    {
        $subscribers = Subscriber::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            return response()->json(['message' => 'No hay suscriptores activos.'], 200);
        }

        $sent = 0;
        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new EmailTemplateMail($email, $subscriber));
                $sent++;
            } catch (\Exception $e) {
                // Loguear el error pero continuar con los demás
                \Log::error("Error enviando email a {$subscriber->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message'    => "Email enviado a {$sent} de {$subscribers->count()} suscriptores.",
            'sent'       => $sent,
            'total'      => $subscribers->count(),
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
