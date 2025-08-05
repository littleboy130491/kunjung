<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_host_verified' => 'boolean',
            'last_active_at' => 'datetime',
        ];
    }


    /**
     * Properties owned by this user (for hosts)
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'host_id');
    }

    /**
     * Bookings made by this user (for guests)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    /**
     * Reviews written by this user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * Check if user can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    /**
     * Check if user is a host
     */
    public function isHost(): bool
    {
        return $this->hasRole('host');
    }

    /**
     * Check if user is a verified host
     */
    public function isVerifiedHost(): bool
    {
        return $this->isHost() && $this->is_host_verified;
    }

    /**
     * Get user's profile photo URL (stored as direct URL)
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->attributes['profile_photo_url'] ?? null;
    }

    /**
     * Get user's avatar URL (stored as direct URL)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->attributes['avatar_url'] ?? $this->profile_photo_url;
    }

    /**
     * Get host verification status badge
     */
    public function getVerificationBadgeAttribute(): string
    {
        if (!$this->isHost()) {
            return 'guest';
        }

        return match ($this->host_verification_status) {
            'verified' => 'verified-host',
            'pending' => 'pending-verification',
            'rejected' => 'verification-rejected',
            default => 'unverified-host',
        };
    }
}
