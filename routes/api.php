<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClipController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\SubscriberController;

// ═══════════════════════════════════════════════════════
// RUTAS PÚBLICAS (sin autenticación)
// ═══════════════════════════════════════════════════════

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Catálogo público
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/clips', [ClipController::class, 'index']);
Route::get('/clips/{clip}', [ClipController::class, 'show']);
Route::get('/networks', [NetworkController::class, 'index']);
Route::get('/site-settings', [SiteSettingController::class, 'get']);

// Suscripción pública
Route::post('/subscribe', [SubscriberController::class, 'store']);

// ═══════════════════════════════════════════════════════
// RUTAS PROTEGIDAS (requieren token Sanctum)
// ═══════════════════════════════════════════════════════

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ──────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $r) => $r->user());
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/register', [AuthController::class, 'register']);

    // ── Categorías ────────────────────────────
    Route::apiResource('categories', CategoryController::class)
        ->except(['index', 'show']);  // index y show son públicos
    // Como usamos POST para actualizar (multipart), definimos la ruta manualmente
    Route::post('/categories/{category}', [CategoryController::class, 'update']);

    // ── Clips ─────────────────────────────────
    Route::apiResource('clips', ClipController::class)
        ->except(['index', 'show']);
    Route::post('/clips/{clip}', [ClipController::class, 'update']);
    Route::patch('/clips/{clip}/status', [ClipController::class, 'changeStatus']);

    // ── Emails (plantillas) ───────────────────
    Route::apiResource('emails', EmailController::class);
    Route::post('/emails/{email}', [EmailController::class, 'update']);
    Route::post('/emails/{email}/send', [EmailController::class, 'send']);

    // ── Redes sociales ────────────────────────
    Route::apiResource('networks', NetworkController::class)
        ->except(['index']);
    Route::post('/networks/{network}', [NetworkController::class, 'update']);

    // ── Configuración del sitio ───────────────
    Route::post('/site-settings', [SiteSettingController::class, 'update']);

    // ── Suscriptores (admin) ──────────────────
    Route::get('/subscribers', [SubscriberController::class, 'index']);
    Route::get('/subscribers/{subscriber}', [SubscriberController::class, 'show']);
    Route::put('/subscribers/{subscriber}', [SubscriberController::class, 'update']);
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy']);
    Route::patch('/subscribers/{subscriber}/toggle', [SubscriberController::class, 'toggle']);
});