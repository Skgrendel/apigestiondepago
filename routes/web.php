<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Rutas para webhooks de Epayco
 */
Route::prefix('api/webhooks')->group(function () {
    // Recibir transacción de Epayco
    Route::post('/epayco/transaction', [WebhookController::class, 'handleEpaycoTransaction']);

    // Verificar estado de una transacción
    Route::get('/transaction/{transactionId}', [WebhookController::class, 'getWebhookStatus']);
});

