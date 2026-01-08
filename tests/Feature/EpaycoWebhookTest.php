<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\EpaycoTransaction;
use App\Services\EpaycoWebhookService;

class EpaycoWebhookTest extends TestCase
{
    /**
     * Test para procesar un webhook de Epayco exitoso
     */
    public function test_procesar_webhook_epayco_exitoso()
    {
        $payload = [
            'x_cust_id_cliente' => '1562497',
            'x_ref_payco' => '328994305',
            'x_id_factura' => '95fb7ac73e71021130302e7-1767880620',
            'x_description' => 'Test transacción',
            'x_amount' => '600000',
            'x_amount_country' => '600000',
            'x_amount_ok' => '600000',
            'x_tax' => '0',
            'x_amount_base' => '600000',
            'x_currency_code' => 'COP',
            'x_respuesta' => 'Aceptada',
            'x_cod_respuesta' => '1',
            'x_transaction_id' => '328994305',
            'x_transaction_date' => '2026-01-08 08:57:01',
            'x_franchise' => 'VS',
            'x_business' => 'ACOFICUM',
            'x_customer_doctype' => 'CC',
            'x_customer_document' => '1143441158',
            'x_customer_name' => 'Maria',
            'x_customer_lastname' => 'Angulo',
            'x_customer_email' => 'test@example.com',
            'x_customer_phone' => '3006049029',
            'x_customer_country' => 'CO',
            'x_customer_city' => 'Bogota',
        ];

        // Procesar la transacción
        $service = new EpaycoWebhookService();
        $transaction = $service->processTransaction($payload);

        // Verificaciones
        $this->assertNotNull($transaction);
        $this->assertEquals('328994305', $transaction->transaction_id);
        $this->assertEquals('ACEPTADA', $transaction->status);
        $this->assertEquals('600000', $transaction->amount);
        $this->assertEquals('test@example.com', $transaction->email);

        // Verificar que se guardó en la BD
        $dbTransaction = EpaycoTransaction::where('transaction_id', '328994305')->first();
        $this->assertNotNull($dbTransaction);
    }

    /**
     * Test para enviar POST al webhook
     */
    public function test_post_webhook_epayco()
    {
        $payload = [
            'x_transaction_id' => '999888777',
            'x_respuesta' => 'Aceptada',
            'x_cod_respuesta' => '1',
            'x_amount' => '100000',
            'x_ref_payco' => 'REF001',
            'x_franchise' => 'VS',
            'x_customer_email' => 'webhook@test.com',
            'x_customer_name' => 'Test',
            'x_customer_lastname' => 'User',
            'x_customer_phone' => '3000000000',
            'x_customer_city' => 'Bogota',
            'x_customer_country' => 'CO',
            'x_customer_document' => '1234567890',
            'x_business' => 'ACOFICUM',
            'x_description' => 'Transacción de prueba',
        ];

        $response = $this->post('/api/webhooks/epayco/transaction', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * Test para consultar estado de transacción
     */
    public function test_consultar_estado_transaccion()
    {
        // Crear una transacción de prueba
        $transaction = EpaycoTransaction::create([
            'transaction_id' => 'TEST123456',
            'status' => 'ACEPTADA',
            'amount' => 500000,
            'reference' => 'REF123',
            'payment_method' => 'VS',
            'email' => 'test@example.com',
            'description' => 'Test transaction',
            'raw_data' => [],
        ]);

        // Consultar estado
        $response = $this->get("/api/webhooks/transaction/TEST123456");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'transaction' => [
                'transaction_id' => 'TEST123456',
                'status' => 'ACEPTADA',
            ],
        ]);
    }
}
