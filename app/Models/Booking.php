<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'property_id',
        'guest_id',
        'booking_reference',
        'check_in_date',
        'check_out_date',
        'guests_adult',
        'guests_children',
        'guests_infants',
        'guests_pets',
        'total_nights',
        'base_price',
        'weekend_nights',
        'weekend_price',
        'cleaning_fee',
        'service_fee',
        'security_deposit',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'special_requests',
        'guest_message',
        'host_message',
        'check_in_instructions',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'guest_name',
        'guest_email',
        'guest_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'estimated_arrival_time',
        'actual_check_in_time',
        'actual_check_out_time',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'base_price' => 'decimal:2',
            'weekend_price' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'estimated_arrival_time' => 'datetime',
            'actual_check_in_time' => 'datetime',
            'actual_check_out_time' => 'datetime',
        ];
    }

    /**
     * Booking belongs to a property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Booking belongs to a guest (user)
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    /**
     * Booking has payment transactions
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Booking may have a review
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Generate unique booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_reference) {
                $booking->booking_reference = 'KNJ' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get total guests count
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->guests_adult + $this->guests_children + $this->guests_infants;
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed' && $this->payment_status === 'paid';
    }

    /**
     * Check if booking is active (guest is currently staying)
     */
    public function isActive(): bool
    {
        return $this->status === 'confirmed' 
            && now()->between($this->check_in_date, $this->check_out_date);
    }

    /**
     * Check if booking is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' 
            || ($this->status === 'confirmed' && now()->isAfter($this->check_out_date));
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if (in_array($this->status, ['cancelled', 'completed'])) {
            return false;
        }

        // Check cancellation policy
        $policy = $this->property->cancellation_policy ?? 'moderate';
        $hoursBeforeCheckIn = now()->diffInHours($this->check_in_date, false);

        return match ($policy) {
            'flexible' => $hoursBeforeCheckIn >= 24,
            'moderate' => $hoursBeforeCheckIn >= 120, // 5 days
            'strict' => $hoursBeforeCheckIn >= 336,   // 14 days
            default => $hoursBeforeCheckIn >= 24,
        };
    }

    /**
     * Calculate refund amount based on cancellation policy
     */
    public function calculateRefundAmount(): float
    {
        if (!$this->canBeCancelled()) {
            return 0;
        }

        $policy = $this->property->cancellation_policy ?? 'moderate';
        $hoursBeforeCheckIn = now()->diffInHours($this->check_in_date, false);
        
        $refundPercentage = match ($policy) {
            'flexible' => $hoursBeforeCheckIn >= 24 ? 100 : 0,
            'moderate' => $hoursBeforeCheckIn >= 120 ? 100 : ($hoursBeforeCheckIn >= 24 ? 50 : 0),
            'strict' => $hoursBeforeCheckIn >= 336 ? 100 : ($hoursBeforeCheckIn >= 168 ? 50 : 0),
            default => $hoursBeforeCheckIn >= 24 ? 100 : 0,
        };

        // Service fee is typically non-refundable
        $refundableAmount = $this->total_amount - $this->service_fee;
        
        return ($refundableAmount * $refundPercentage) / 100;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get booking duration in nights
     */
    public function getDurationAttribute(): int
    {
        return $this->check_out_date->diffInDays($this->check_in_date);
    }

    /**
     * Get booking status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'completed' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Scope for bookings by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now())
            ->where('status', 'confirmed');
    }

    /**
     * Scope for active bookings (guests currently staying)
     */
    public function scopeActive($query)
    {
        return $query->where('check_in_date', '<=', now())
            ->where('check_out_date', '>', now())
            ->where('status', 'confirmed');
    }

    /**
     * Scope for completed bookings
     */
    public function scopeCompleted($query)
    {
        return $query->where('check_out_date', '<=', now())
            ->whereIn('status', ['confirmed', 'completed']);
    }
}