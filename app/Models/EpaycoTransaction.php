<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EpaycoTransaction extends Model
{
    use HasFactory;

    protected $table = 'epayco_transactions';

    protected $fillable = [
        'transaction_id',
        'status',
        'amount',
        'reference',
        'payment_method',
        'email',
        'description',
        'raw_data',
        'moodle_user_id',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener transacciones aprobadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'ACEPTADA');
    }

    /**
     * Obtener transacciones rechazadas
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'RECHAZADA');
    }

    /**
     * Obtener transacciones pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDIENTE');
    }

    /**
     * Obtener transacciones con error
     */
    public function scopeWithError($query)
    {
        return $query->where('status', 'ERROR');
    }

    /**
     * Obtener transacciones por método de pago
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Obtener transacciones por rango de fecha
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Obtener transacciones por email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Obtener transacciones por referencia
     */
    public function scopeByReference($query, $reference)
    {
        return $query->where('reference', $reference);
    }

    /**
     * Verificar si la transacción fue aprobada
     */
    public function isApproved(): bool
    {
        return $this->status === 'ACEPTADA';
    }

    /**
     * Verificar si la transacción fue rechazada
     */
    public function isRejected(): bool
    {
        return $this->status === 'RECHAZADA';
    }

    /**
     * Verificar si la transacción está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDIENTE';
    }

    /**
     * Obtener estado legible
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'ACEPTADA' => 'Aprobado',
            'RECHAZADA' => 'Rechazado',
            'PENDIENTE' => 'Pendiente',
            'ERROR' => 'Error',
        ];

        return $labels[$this->status] ?? 'Desconocido';
    }
}
