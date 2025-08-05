<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'transaction_id',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'payment_type',
        'payment_method',
        'gross_amount',
        'currency',
        'transaction_status',
        'transaction_time',
        'settlement_time',
        'fraud_status',
        'status_code',
        'status_message',
        'midtrans_response',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'va_number',
        'bank',
        'bill_key',
        'biller_code',
        'pdf_url',
        'expired_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'transaction_time' => 'datetime',
            'settlement_time' => 'datetime',
            'refunded_at' => 'datetime',
            'expired_at' => 'datetime',
            'midtrans_response' => 'array',
        ];
    }

    /**
     * Payment transaction belongs to a booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this->transaction_status, ['capture', 'settlement']);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Check if transaction failed
     */
    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'failure']);
    }

    /**
     * Check if transaction is refunded
     */
    public function isRefunded(): bool
    {
        return !is_null($this->refund_amount) && $this->refund_amount > 0;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->gross_amount, 0, ',', '.');
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match ($this->payment_method) {
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'echannel' => 'Mandiri Bill Payment',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'other_va' => 'Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'indomaret' => 'Indomaret',
            'alfamart' => 'Alfamart',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->transaction_status) {
            'capture', 'settlement' => 'badge-success',
            'pending' => 'badge-warning',
            'deny', 'cancel', 'expire', 'failure' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Scope for successful transactions
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('transaction_status', ['capture', 'settlement']);
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('transaction_status', 'pending');
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('transaction_status', ['deny', 'cancel', 'expire', 'failure']);
    }
}