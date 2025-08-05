<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LocalGuide extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'title',
        'description',
        'category',
        'type',
        'location',
        'address',
        'latitude',
        'longitude',
        'distance_km',
        'estimated_travel_time',
        'price_range',
        'rating',
        'phone',
        'website',
        'opening_hours',
        'is_featured',
        'is_verified',
        'sort_order',
        'photos',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'distance_km' => 'decimal:2',
            'rating' => 'decimal:1',
            'opening_hours' => 'array',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'photos' => 'array',
        ];
    }


    /**
     * Local guide belongs to a property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Scope for featured guides
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}