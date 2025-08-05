<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'host_id',
        'title',
        'slug',
        'description',
        'short_description',
        'property_type',
        'service_types',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'beds',
        'location',
        'address',
        'city',
        'region',
        'postal_code',
        'latitude',
        'longitude',
        'base_price_per_night',
        'weekend_price_per_night',
        'cleaning_fee',
        'service_fee',
        'security_deposit',
        'minimum_stay_nights',
        'maximum_stay_nights',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'house_rules',
        'space_description',
        'guest_access',
        'interaction_style',
        'neighborhood_overview',
        'transit_info',
        'is_published',
        'is_featured',
        'is_instant_book',
        'status',
        'authenticity_verified',
        'last_booked_at',
        'average_rating',
        'total_reviews',
        'response_rate',
        'view_count',
        'favorite_count',
        'meta_title',
        'meta_description',
        'seo_keywords',
        'cover_photo_url',
        'gallery_photos',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'service_types' => 'array',
            'base_price_per_night' => 'decimal:2',
            'weekend_price_per_night' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_instant_book' => 'boolean',
            'authenticity_verified' => 'boolean',
            'last_booked_at' => 'datetime',
            'average_rating' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'gallery_photos' => 'array',
        ];
    }


    /**
     * Property belongs to a host (user)
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Property has many bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Property has many reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Property has many amenities (many-to-many)
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities');
    }

    /**
     * Property has many local guides
     */
    public function localGuides(): HasMany
    {
        return $this->hasMany(LocalGuide::class);
    }

    /**
     * Property availability calendar
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(PropertyAvailability::class);
    }

    /**
     * Users who favorited this property
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_favorites')
            ->withTimestamps();
    }

    /**
     * Get the property's cover photo URL (stored as direct URL)
     */
    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->attributes['cover_photo_url'] ?? 
            (is_array($this->gallery_photos) && count($this->gallery_photos) > 0 
                ? $this->gallery_photos[0]['url'] ?? null 
                : null);
    }

    /**
     * Get the property's thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->cover_photo_url;
    }

    /**
     * Get all gallery photos (stored as array of URLs/objects)
     */
    public function getGalleryPhotosAttribute(): array
    {
        return $this->attributes['gallery_photos'] ?? [];
    }

    /**
     * Check if property supports specific service type
     */
    public function supportsServiceType(string $type): bool
    {
        return in_array($type, $this->service_types ?? []);
    }

    /**
     * Get formatted price per night
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price_per_night, 0, ',', '.');
    }

    /**
     * Get total price for given dates (including fees)
     */
    public function getTotalPrice(int $nights, bool $isWeekend = false): float
    {
        $basePrice = $isWeekend ? $this->weekend_price_per_night : $this->base_price_per_night;
        $stayTotal = $basePrice * $nights;
        
        return $stayTotal + $this->cleaning_fee + $this->service_fee;
    }

    /**
     * Check if property is available for given dates
     */
    public function isAvailable(string $checkIn, string $checkOut): bool
    {
        // Check for conflicting bookings
        $conflicts = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                          ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->exists();

        return !$conflicts;
    }

    /**
     * Get property location for maps
     */
    public function getLocationAttribute(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'city' => $this->city,
            'region' => $this->region,
        ];
    }

    /**
     * Scope for published properties
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'active');
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for properties by service type
     */
    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->whereJsonContains('service_types', $serviceType);
    }

    /**
     * Scope for properties in location
     */
    public function scopeInLocation($query, string $location)
    {
        return $query->where('city', 'LIKE', "%{$location}%")
            ->orWhere('region', 'LIKE', "%{$location}%");
    }
}