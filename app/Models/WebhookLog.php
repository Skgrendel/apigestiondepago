<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhookLog extends Model
{
    use HasFactory;

    protected $table = 'webhook_logs';

    protected $fillable = [
        'provider',
        'event_type',
        'payload',
        'response',
        'status',
        'transaction_id',
        'ip_address',
        'headers',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'headers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener webhooks por tipo de evento
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Obtener webhooks exitosos
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Obtener webhooks fallidos
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
