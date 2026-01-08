<?php

namespace App\Http\Controllers;

use App\Models\EpaycoTransaction;
use App\Models\WebhookLog;
use App\Services\EpaycoWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected EpaycoWebhookService $webhookService;

    public function __construct(EpaycoWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Recibir webhook de transacción de Epayco
     */
    public function handleEpaycoTransaction(Request $request): JsonResponse
    {
        //TODO: falta validar el id del plan corresponda con el plan comprado
        try {
            Log::info($request->method().' - Webhook recibido de Epayco', $request->all());
            //validar que el referencia de pago ya fue usada
            $existingTransaction = EpaycoTransaction::where('reference', $request->input('x_ref_payco'))->first();
            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'La referencia de pago ya ha sido utilizada',
                ], 400);
            }

            // Registrar el webhook recibido
            $webhookLog = WebhookLog::create([
                'provider' => 'epayco',
                'event_type' => 'transaction',
                'payload' => $request->all(),
                'ip_address' => $request->ip(),
                'status' => 'received',
            ]);

            // Procesar la transacción
            $transaction = $this->webhookService->processTransaction($request->all());

            // Actualizar el log como procesado exitosamente
            $webhookLog->update([
                'status' => 'processed',
                'transaction_id' => $transaction->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transacción procesada correctamente',
                'transaction_id' => $transaction->id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook de Epayco', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            // Registrar el error
            if (isset($webhookLog)) {
                $webhookLog->update([
                    'status' => 'error',
                    'response' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error procesando la transacción',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verificar el estado de un webhook
     */
    public function getWebhookStatus(string $transactionId): JsonResponse
    {
        $transaction = EpaycoTransaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transacción no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'email' => $transaction->email,
                'created_at' => $transaction->created_at,
            ],
        ], 200);
    }
}
