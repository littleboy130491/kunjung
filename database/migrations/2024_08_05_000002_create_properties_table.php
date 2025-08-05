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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            
            // Basic information
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->text('short_description')->nullable();
            $table->enum('property_type', ['villa', 'house', 'apartment', 'resort', 'hotel', 'guesthouse', 'other']);
            $table->json('service_types'); // ['stays', 'wedding', 'photo_shoot']
            
            // Capacity
            $table->integer('max_guests');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('beds');
            
            // Location
            $table->string('location');
            $table->text('address');
            $table->string('city');
            $table->string('region');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Pricing
            $table->decimal('base_price_per_night', 10, 2);
            $table->decimal('weekend_price_per_night', 10, 2)->nullable();
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            
            // Booking rules
            $table->integer('minimum_stay_nights')->default(1);
            $table->integer('maximum_stay_nights')->nullable();
            $table->time('check_in_time')->default('15:00:00');
            $table->time('check_out_time')->default('11:00:00');
            $table->enum('cancellation_policy', ['flexible', 'moderate', 'strict'])->default('moderate');
            
            // Descriptions
            $table->text('house_rules')->nullable();
            $table->text('space_description')->nullable();
            $table->text('guest_access')->nullable();
            $table->text('interaction_style')->nullable();
            $table->text('neighborhood_overview')->nullable();
            $table->text('transit_info')->nullable();
            
            // Status and flags
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_instant_book')->default(false);
            $table->enum('status', ['draft', 'active', 'inactive', 'suspended'])->default('draft');
            $table->boolean('authenticity_verified')->default(false);
            
            // Analytics
            $table->timestamp('last_booked_at')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('response_rate', 5, 2)->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('seo_keywords')->nullable();

            // Photo URLs (for Filament Curator compatibility)
            $table->string('cover_photo_url')->nullable();
            $table->json('gallery_photos')->nullable(); // Array of photo objects
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('host_id');
            $table->index('is_published');
            $table->index('is_featured');
            $table->index('property_type');
            $table->index('city');
            $table->index('region');
            $table->index('status');
            $table->index(['latitude', 'longitude']);
            $table->index('base_price_per_night');
            $table->index('average_rating');
            // Note: SQLite doesn't support fulltext indexes
            // Use regular indexes for basic search performance
            $table->index('title');
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};