<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'booking_id',
        'user_id',
        'rating_overall',
        'rating_cleanliness',
        'rating_location',
        'rating_value',
        'rating_communication',
        'rating_check_in',
        'rating_accuracy',
        'review_text',
        'host_response',
        'is_verified',
        'is_featured',
        'is_approved',
        'helpful_count',
        'reported_count',
        'language',
        'stays_count',
        'review_photos',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rating_overall' => 'decimal:1',
            'rating_cleanliness' => 'decimal:1',
            'rating_location' => 'decimal:1',
            'rating_value' => 'decimal:1',
            'rating_communication' => 'decimal:1',
            'rating_check_in' => 'decimal:1',
            'rating_accuracy' => 'decimal:1',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'review_photos' => 'array',
        ];
    }


    /**
     * Review belongs to a property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Review belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Review belongs to a booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get review photos (stored as array)
     */
    public function getReviewPhotosAttribute(): array
    {
        return $this->attributes['review_photos'] ?? [];
    }

    /**
     * Get average rating from all individual ratings
     */
    public function getAverageRatingAttribute(): float
    {
        $ratings = [
            $this->rating_cleanliness,
            $this->rating_location,
            $this->rating_value,
            $this->rating_communication,
            $this->rating_check_in,
            $this->rating_accuracy,
        ];

        $validRatings = array_filter($ratings, fn($rating) => $rating !== null);
        
        return count($validRatings) > 0 ? round(array_sum($validRatings) / count($validRatings), 1) : 0;
    }

    /**
     * Get star rating display
     */
    public function getStarRatingAttribute(): string
    {
        $rating = $this->rating_overall;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return str_repeat('★', $fullStars) . 
               str_repeat('☆', $halfStar) . 
               str_repeat('☆', $emptyStars);
    }

    /**
     * Get review excerpt for preview
     */
    public function getExcerptAttribute(): string
    {
        return strlen($this->review_text) > 150 
            ? substr($this->review_text, 0, 150) . '...'
            : $this->review_text;
    }

    /**
     * Check if review has photos
     */
    public function hasPhotos(): bool
    {
        return count($this->review_photos) > 0;
    }

    /**
     * Check if review is positive (4+ stars)
     */
    public function isPositive(): bool
    {
        return $this->rating_overall >= 4.0;
    }

    /**
     * Check if review needs host response
     */
    public function needsHostResponse(): bool
    {
        return empty($this->host_response) && $this->is_approved;
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for reviews with photos
     */
    public function scopeWithPhotos($query)
    {
        return $query->whereNotNull('review_photos')
            ->where('review_photos', '!=', '[]');
    }

    /**
     * Scope for recent reviews
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}