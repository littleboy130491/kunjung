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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->constrained('users')->onDelete('cascade');
            
            // Booking reference
            $table->string('booking_reference')->unique();
            
            // Dates and guests
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('guests_adult')->default(1);
            $table->integer('guests_children')->default(0);
            $table->integer('guests_infants')->default(0);
            $table->integer('guests_pets')->default(0);
            $table->integer('total_nights');
            
            // Pricing breakdown
            $table->decimal('base_price', 10, 2);
            $table->integer('weekend_nights')->default(0);
            $table->decimal('weekend_price', 10, 2)->default(0);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('IDR');
            
            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'refunded', 'failed'])->default('unpaid');
            $table->string('payment_method')->nullable();
            
            // Messages and requests
            $table->text('special_requests')->nullable();
            $table->text('guest_message')->nullable();
            $table->text('host_message')->nullable();
            $table->text('check_in_instructions')->nullable();
            
            // Important timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            
            // Guest information
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Check-in/out tracking
            $table->time('estimated_arrival_time')->nullable();
            $table->timestamp('actual_check_in_time')->nullable();
            $table->timestamp('actual_check_out_time')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('property_id');
            $table->index('guest_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['check_in_date', 'check_out_date']);
            $table->index('booking_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};