<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpaycoWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'transactionId' => 'required|string',
            'status' => 'required|string|in:ACEPTADA,RECHAZADA,PENDIENTE,ERROR',
            'amount' => 'required|numeric|min:0',
            'reference' => 'required|string',
            'paymentMethod' => 'required|string',
            'email' => 'required|email',
            'timestamp' => 'required|date_format:Y-m-d H:i:s',
            'signature' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'transactionId.required' => 'ID de transacción es requerido',
            'status.required' => 'Estado del pago es requerido',
            'status.in' => 'Estado inválido: debe ser ACEPTADA, RECHAZADA, PENDIENTE o ERROR',
            'amount.required' => 'Monto es requerido',
            'amount.numeric' => 'Monto debe ser un número válido',
            'reference.required' => 'Referencia es requerida',
            'paymentMethod.required' => 'Método de pago es requerido',
            'email.required' => 'Email es requerido',
            'email.email' => 'Email inválido',
            'timestamp.required' => 'Timestamp es requerido',
            'timestamp.date_format' => 'Formato de fecha inválido (debe ser: YYYY-MM-DD HH:MM:SS)',
        ];
    }
}
