<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Rutas para webhooks de Epayco
 */

Route::prefix('/webhooks')->group(function () {
    // Recibir transacción de Epayco
    Route::match(['get', 'post'], '/epayco/transaction', [WebhookController::class, 'handleEpaycoTransaction']);

    // Verificar estado de una transacción
    Route::get('/transaction/{transactionId}', [WebhookController::class, 'getWebhookStatus']);

});
