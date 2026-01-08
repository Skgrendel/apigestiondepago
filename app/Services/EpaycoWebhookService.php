<?php

namespace App\Services;

use App\Models\EpaycoTransaction;
use Illuminate\Support\Facades\Log;

class EpaycoWebhookService
{
    /**
     * Procesar transacción de Epayco
     */
    public function processTransaction(array $payload): EpaycoTransaction
    {
        // Validar que la respuesta sea aceptada
        $isApproved = $this->validateApprovedTransaction($payload);

        // Crear o actualizar la transacción
        $transaction = EpaycoTransaction::updateOrCreate(
            ['transaction_id' => $payload['x_transaction_id']],
            [
                'status' => $this->mapStatus($payload),
                'amount' => (float) $payload['x_amount'],
                'reference' => $payload['x_ref_payco'] ?? null,
                'payment_method' => $payload['x_franchise'] ?? null,
                'email' => $payload['x_customer_email'] ?? null,
                'description' => $payload['x_description'] ?? null,
                'raw_data' => $payload,
            ]
        );

        Log::info('Transacción de Epayco procesada', [
            'transaction_id' => $transaction->transaction_id,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
        ]);

        // Si la transacción fue aprobada, procesar acciones adicionales
        if ($isApproved) {
            $this->handleApprovedTransaction($transaction, $payload);
        }

        return $transaction;
    }

    /**
     * Validar si la transacción fue aprobada
     */
    private function validateApprovedTransaction(array $payload): bool
    {
        // Epayco envía código de respuesta "1" para aprobada
        return isset($payload['x_cod_respuesta']) && $payload['x_cod_respuesta'] == '1' &&
               isset($payload['x_respuesta']) && strtoupper($payload['x_respuesta']) === 'ACEPTADA';
    }

    /**
     * Mapear el estado de la respuesta de Epayco al formato interno
     */
    private function mapStatus(array $payload): string
    {
        $response = strtoupper($payload['x_respuesta'] ?? '');

        return match ($response) {
            'Aceptada' => 'Aceptada',
            'Rechazada' => 'Rechazada',
            'Pendiente' => 'Pendiente',
            default => 'Desconocido',
        };
    }

    /**
     * Manejar acciones cuando la transacción es aprobada
     */
    private function handleApprovedTransaction(EpaycoTransaction $transaction, array $payload): void
    {
        try {
            Log::info('Procesando transacción aprobada', [
                'transaction_id' => $transaction->transaction_id,
                'email' => $payload['x_customer_email'] ?? null,
            ]);

            // 1. Crear usuario en Moodle y enrolarlo
            $moodleService = new MoodleEnrollmentService();
            $enrollmentResult = $moodleService->processEpaycoEnrollment($payload);

            if ($enrollmentResult['success']) {
                Log::info('Usuario creado y enrolado en Moodle exitosamente', [
                    'transaction_id' => $transaction->transaction_id,
                    'moodle_user_id' => $enrollmentResult['moodle_user_id'],
                    'email' => $payload['x_customer_email'] ?? null,
                ]);

                // Guardar las credenciales si el usuario fue creado
                if (isset($enrollmentResult['credentials'])) {
                    $transaction->update([
                        'moodle_user_id' => $enrollmentResult['moodle_user_id'],
                    ]);
                }
            } else {
                Log::warning('Error al crear usuario en Moodle', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $enrollmentResult['error'],
                    'step' => $enrollmentResult['step'],
                ]);
            }

            // 2. TODO: Enviar correo de confirmación
            // sendConfirmationEmail($payload, $enrollmentResult)

        } catch (\Exception $e) {
            Log::error('Error procesando transacción aprobada', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Obtener información de la transacción para enviar a Moodle
     */
    public function getMoodleUserData(array $payload): array
    {
        return [
            'email' => $payload['x_customer_email'] ?? '',
            'firstname' => $payload['x_customer_name'] ?? '',
            'lastname' => $payload['x_customer_lastname'] ?? '',
            'phone' => $payload['x_customer_phone'] ?? '',
            'city' => $payload['x_customer_city'] ?? '',
            'country' => $payload['x_customer_country'] ?? 'CO',
            'idnumber' => $payload['x_customer_document'] ?? '',
            'department' => $payload['x_description'] ?? '', // Tipo de membresía
            'institution' => $payload['x_business'] ?? '',
        ];
    }
}
