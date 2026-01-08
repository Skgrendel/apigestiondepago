<?php

use Illuminate\Support\Facades\Http;
use App\Services\EpaycoWebhookService;

/**
 * Script de prueba del webhook de Epayco
 *
 * Uso:
 * php artisan tinker
 * > include 'tests/epayco_webhook_test.php'
 */

// Datos de prueba - Similar al JSON proporcionado
$testPayload = [
    'x_cust_id_cliente' => '1562497',
    'x_ref_payco' => '328994305',
    'x_id_factura' => '95fb7ac73e71021130302e7-1767880620',
    'x_id_invoice' => '95fb7ac73e71021130302e7-1767880620',
    'x_description' => 'Pensado para profesionales que buscan desarrollo integral',
    'x_amount' => '600000',
    'x_amount_country' => '600000',
    'x_amount_ok' => '600000',
    'x_tax' => '0',
    'x_amount_base' => '600000',
    'x_currency_code' => 'COP',
    'x_bank_name' => '',
    'x_cardnumber' => '457562*******0326',
    'x_quotas' => '1',
    'x_respuesta' => 'Aceptada',
    'x_response' => 'Aceptada',
    'x_approval_code' => '000000',
    'x_transaction_id' => '328994305',
    'x_fecha_transaccion' => '2026-01-08 08:57:01',
    'x_transaction_date' => '2026-01-08 08:57:01',
    'x_cod_respuesta' => '1',
    'x_cod_response' => '1',
    'x_response_reason_text' => '00-Aprobada',
    'x_errorcode' => '00',
    'x_cod_transaction_state' => '1',
    'x_transaction_state' => 'Aceptada',
    'x_franchise' => 'VS',
    'x_business' => 'ASOCIACIÓN COLOMBIANA DE OFICIALES DE CUMPLIMIENTO',
    'x_customer_doctype' => 'CC',
    'x_customer_document' => '1143441158',
    'x_customer_name' => 'Maria Alexis',
    'x_customer_lastname' => 'Angulo',
    'x_customer_email' => 'stevenscarrascal@hotmail.com',
    'x_customer_phone' => '3006049029',
    'x_customer_movil' => '0000000000',
    'x_customer_ind_pais' => '',
    'x_customer_country' => 'CO',
    'x_customer_city' => 'Bogotá',
    'x_customer_address' => 'Calle 123',
    'x_customer_ip' => '34.196.55.154',
    'x_test_request' => 'TRUE',
    'x_extra1' => '95fb7ac73e71021130302e7',
    'x_extra2' => '95fb7ace61746926f08e037',
    'x_extra3' => '95fa909c990e67ae102cee0',
    'x_extra4' => '0',
    'x_extra5' => '0',
    'x_extra6' => '',
    'x_extra9' => '1562497',
    'x_extra7' => '',
    'x_extra8' => '',
    'x_extra10' => '',
    'x_extra4_epayco' => 'no-edata',
    'x_tax_ico' => '0',
    'x_payment_date' => '2026-01-08 08:57:02',
    'x_signature' => '2a92d05ec428d431e7cce71831ab3f18d648a19af82932624e37469ff1ab6ddc',
    'x_transaction_cycle' => '',
    'is_processable' => '1',
];

echo "=== PRUEBA DE WEBHOOK DE EPAYCO ===\n\n";

// Test 1: Procesar transacción directamente
echo "Test 1: Procesando transacción...\n";
$service = new EpaycoWebhookService();
$transaction = $service->processTransaction($testPayload);

echo "✅ Transacción procesada:\n";
echo "   ID: {$transaction->id}\n";
echo "   Transaction ID: {$transaction->transaction_id}\n";
echo "   Status: {$transaction->status}\n";
echo "   Amount: {$transaction->amount}\n";
echo "   Email: {$transaction->email}\n\n";

// Test 2: Enviar HTTP POST al webhook
echo "Test 2: Enviando POST al endpoint webhook...\n";
$response = Http::post('http://apigestiondepago.test/api/webhooks/epayco/transaction', $testPayload);

echo "Status: " . $response->status() . "\n";
echo "Response:\n";
echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 3: Consultar estado de la transacción
echo "Test 3: Consultando estado de transacción...\n";
$statusResponse = Http::get('http://apigestiondepago.test/api/webhooks/transaction/328994305');

echo "Status: " . $statusResponse->status() . "\n";
echo "Response:\n";
echo json_encode($statusResponse->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 4: Verificar que el usuario fue creado en Moodle
echo "Test 4: Verificando registros en BD...\n";
$dbTransaction = \App\Models\EpaycoTransaction::where('transaction_id', '328994305')->first();
if ($dbTransaction) {
    echo "✅ Transacción encontrada en BD:\n";
    echo "   ID BD: {$dbTransaction->id}\n";
    echo "   Moodle User ID: {$dbTransaction->moodle_user_id}\n";
    echo "   Status: {$dbTransaction->status}\n";
} else {
    echo "❌ Transacción no encontrada\n";
}

echo "\n=== FIN DE PRUEBAS ===\n";
