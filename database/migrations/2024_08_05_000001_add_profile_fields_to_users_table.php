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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            
            // Host-specific fields
            $table->boolean('is_host_verified')->default(false);
            $table->enum('host_verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified');
            $table->string('host_document_path')->nullable();
            $table->decimal('response_rate', 5, 2)->nullable(); // Percentage
            $table->integer('response_time_hours')->nullable();
            $table->timestamp('last_active_at')->nullable();

            // Photo URLs (for Filament Curator compatibility)
            $table->string('profile_photo_url')->nullable();
            $table->string('avatar_url')->nullable();
            
            $table->index('is_host_verified');
            $table->index('host_verification_status');
            $table->index('last_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite doesn't support dropping columns easily
        // For development, use: php artisan migrate:fresh
        // For production, would need to recreate table
        
        if (config('database.default') !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'phone',
                    'date_of_birth',
                    'gender',
                    'bio',
                    'location',
                    'is_host_verified',
                    'host_verification_status',
                    'host_document_path',
                    'response_rate',
                    'response_time_hours',
                    'last_active_at',
                    'profile_photo_url',
                    'avatar_url',
                ]);
            });
        }
    }
};