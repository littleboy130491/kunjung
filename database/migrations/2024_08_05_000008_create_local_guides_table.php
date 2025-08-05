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
        Schema::create('local_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            
            // Basic information
            $table->string('title');
            $table->text('description');
            $table->enum('category', [
                'restaurant',
                'attraction',
                'activity',
                'shopping',
                'transportation',
                'healthcare',
                'emergency',
                'other'
            ]);
            $table->enum('type', [
                'recommendation',
                'essential',
                'emergency',
                'nearby'
            ]);
            
            // Location
            $table->string('location');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->string('estimated_travel_time')->nullable(); // "5 minutes walk"
            
            // Details
            $table->string('price_range')->nullable(); // "$", "$$", "$$$"
            $table->decimal('rating', 2, 1)->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('opening_hours')->nullable();
            
            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Photos (for Filament Curator compatibility)
            $table->json('photos')->nullable(); // Array of photo objects
            
            $table->timestamps();
            
            // Indexes
            $table->index('property_id');
            $table->index('category');
            $table->index('type');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_guides');
    }
};