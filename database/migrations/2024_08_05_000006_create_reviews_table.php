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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Ratings (1-5 scale)
            $table->decimal('rating_overall', 2, 1);
            $table->decimal('rating_cleanliness', 2, 1)->nullable();
            $table->decimal('rating_location', 2, 1)->nullable();
            $table->decimal('rating_value', 2, 1)->nullable();
            $table->decimal('rating_communication', 2, 1)->nullable();
            $table->decimal('rating_check_in', 2, 1)->nullable();
            $table->decimal('rating_accuracy', 2, 1)->nullable();
            
            // Review content
            $table->text('review_text');
            $table->text('host_response')->nullable();
            
            // Status flags
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(true);
            
            // Engagement metrics
            $table->integer('helpful_count')->default(0);
            $table->integer('reported_count')->default(0);
            
            // Metadata
            $table->string('language', 5)->default('id');
            $table->integer('stays_count')->default(1); // How many times user has stayed at properties
            
            // Photos (for Filament Curator compatibility)
            $table->json('review_photos')->nullable(); // Array of photo objects
            
            $table->timestamps();
            
            // Indexes
            $table->index('property_id');
            $table->index('user_id');
            $table->index('booking_id');
            $table->index('is_approved');
            $table->index('is_featured');
            $table->index('rating_overall');
            $table->unique(['booking_id']); // One review per booking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};