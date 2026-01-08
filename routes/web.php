<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

/**
 * Página de confirmación de pago
 */
Route::get('/pago/confirmacion', function () {
    return view('payment-success');
});

/**
 * Rutas para webhooks de Epayco
 */


