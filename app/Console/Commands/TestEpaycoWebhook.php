<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EpaycoWebhookService;
use App\Models\EpaycoTransaction;

class TestEpaycoWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:test-epayco {--transaction-id=328994305} {--email=test@example.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el webhook de Epayco procesando una transacción de ejemplo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando prueba del webhook de Epayco...\n');

        $transactionId = $this->option('transaction-id');
        $email = $this->option('email');

        // Datos de prueba
        $payload = [
            'x_cust_id_cliente' => '1562497',
            'x_ref_payco' => $transactionId,
            'x_id_factura' => '95fb7ac73e71021130302e7-1767880620',
            'x_description' => 'Transacción de prueba del webhook',
            'x_amount' => '600000',
            'x_amount_country' => '600000',
            'x_amount_ok' => '600000',
            'x_tax' => '0',
            'x_amount_base' => '600000',
            'x_currency_code' => 'COP',
            'x_respuesta' => 'Aceptada',
            'x_cod_respuesta' => '1',
            'x_transaction_id' => $transactionId,
            'x_transaction_date' => now()->format('Y-m-d H:i:s'),
            'x_franchise' => 'VS',
            'x_business' => 'ASOCIACIÓN COLOMBIANA DE OFICIALES DE CUMPLIMIENTO',
            'x_customer_doctype' => 'CC',
            'x_customer_document' => '1143441158',
            'x_customer_name' => 'Test',
            'x_customer_lastname' => 'Usuario',
            'x_customer_email' => $email,
            'x_customer_phone' => '3006049029',
            'x_customer_country' => 'CO',
            'x_customer_city' => 'Bogotá',
        ];

        $this->info('📋 Datos de la transacción:');
        $this->info("   Transaction ID: {$transactionId}");
        $this->info("   Email: {$email}");
        $this->info("   Amount: 600000 COP\n");

        try {
            // Procesar la transacción
            $this->info('⏳ Procesando transacción...');
            $service = new EpaycoWebhookService();
            $transaction = $service->processTransaction($payload);

            $this->info("✅ Transacción procesada correctamente!\n");

            // Mostrar información
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID BD', $transaction->id],
                    ['Transaction ID', $transaction->transaction_id],
                    ['Status', $transaction->status],
                    ['Amount', $transaction->amount],
                    ['Email', $transaction->email],
                    ['Moodle User ID', $transaction->moodle_user_id ?? 'Pendiente'],
                    ['Created at', $transaction->created_at],
                ]
            );

            // Verificar en BD
            $this->info("\n📊 Verificando en base de datos...");
            $dbTransaction = EpaycoTransaction::where('transaction_id', $transactionId)->first();

            if ($dbTransaction) {
                $this->info("✅ Transacción encontrada en BD");
                $this->info("   ID: {$dbTransaction->id}");
                $this->info("   Status: {$dbTransaction->status}");
                $moodleUserId = $dbTransaction->moodle_user_id ?? 'No asignado';
                $this->info("   Moodle User ID: {$moodleUserId}\n");
            }

            $this->info("🎉 ¡Prueba completada exitosamente!");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error procesando la transacción:");
            $this->error($e->getMessage());
            $this->error("\n" . $e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
