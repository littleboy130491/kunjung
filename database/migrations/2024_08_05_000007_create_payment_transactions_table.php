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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Transaction identifiers
            $table->string('transaction_id')->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            
            // Payment details
            $table->string('payment_type')->nullable(); // credit_card, bank_transfer, etc.
            $table->string('payment_method')->nullable(); // specific method like bca_va, gopay
            $table->decimal('gross_amount', 10, 2);
            $table->string('currency', 3)->default('IDR');
            
            // Transaction status from Midtrans
            $table->string('transaction_status')->nullable(); // pending, settlement, capture, etc.
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->string('fraud_status')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status_message')->nullable();
            $table->json('midtrans_response')->nullable();
            
            // Refund information
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Payment method specific fields
            $table->string('va_number')->nullable(); // Virtual Account number
            $table->string('bank')->nullable(); // Bank name for VA/transfer
            $table->string('bill_key')->nullable(); // For convenience store payments
            $table->string('biller_code')->nullable();
            $table->string('pdf_url')->nullable(); // Payment instruction PDF
            $table->timestamp('expired_at')->nullable(); // Payment expiration
            
            $table->timestamps();
            
            // Indexes
            $table->index('booking_id');
            $table->index('transaction_status');
            $table->index('payment_method');
            $table->index('midtrans_transaction_id');
            $table->index('transaction_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};