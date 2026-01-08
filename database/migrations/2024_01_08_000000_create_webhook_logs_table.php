<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('epayco')->index(); // epayco, moodle, etc
            $table->string('event_type')->index();
            $table->longText('payload')->nullable();
            $table->longText('response')->nullable();
            $table->enum('status', ['received', 'processed', 'error', 'pending'])->default('pending')->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index(); // Referencia a epayco_transactions
            $table->string('ip_address')->nullable();
            $table->json('headers')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Índices para búsquedas
            $table->index(['provider', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
