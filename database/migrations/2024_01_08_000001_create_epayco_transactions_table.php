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
        Schema::create('epayco_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->index();
            $table->enum('status', ['ACEPTADA', 'RECHAZADA', 'PENDIENTE', 'ERROR'])->index();
            $table->decimal('amount', 12, 2);
            $table->string('reference')->index();
            $table->string('payment_method'); // VS, MC, AM, PSE, etc.
            $table->string('email');
            $table->text('description')->nullable();
            $table->json('raw_data')->nullable();
            $table->unsignedBigInteger('moodle_user_id')->nullable()->index();
            $table->timestamps();

            // Índices para búsquedas comunes
            $table->index(['status', 'created_at']);
            $table->index(['email', 'created_at']);
            $table->index(['reference', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epayco_transactions');
    }
};
