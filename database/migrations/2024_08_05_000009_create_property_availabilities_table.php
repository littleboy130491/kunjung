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
        Schema::create('property_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('price_override', 10, 2)->nullable(); // Override base price for specific dates
            $table->integer('minimum_stay_override')->nullable(); // Override minimum stay for specific dates
            $table->text('notes')->nullable(); // Internal notes
            $table->timestamps();
            
            // Indexes
            $table->index('property_id');
            $table->index('date');
            $table->index('is_available');
            $table->unique(['property_id', 'date']); // One record per property per date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_availabilities');
    }
};