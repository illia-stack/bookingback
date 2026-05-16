<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BookingStatus;
use App\Models\User;
use App\Models\Property;

class Booking extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'property_id',
        'check_in',
        'check_out',
        'total_price',
        'status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'paid_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'status' => BookingStatus::class,
        'check_in' => 'datetime',   // vorher 'date'
        'check_out' => 'datetime',  // vorher 'date'
        'paid_at' => 'datetime',
        'total_price' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */
    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === BookingStatus::PROCESSING;
    }

    public function isPaid(): bool
    {
        return $this->status === BookingStatus::PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::CANCELLED;
    }

    public function isFailed(): bool
    {
        return $this->status === BookingStatus::FAILED;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC HELPERS
    |--------------------------------------------------------------------------
    */

    // Status setzen auf PROCESSING
    public function markAsProcessing(): self
    {
        $this->update([
            'status' => BookingStatus::PROCESSING,
        ]);

        return $this;
    }

    // Status setzen auf PAID + optionale Payment Intent ID
    public function markAsPaid(?string $paymentIntentId = null): self
    {
        $this->update([
            'status' => BookingStatus::PAID,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $paymentIntentId,
        ]);

        return $this;
    }

    // Status setzen auf FAILED
    public function markAsFailed(): self
    {
        $this->update([
            'status' => BookingStatus::FAILED,
        ]);

        return $this;
    }

    // Berechnet die Dauer der Buchung in Tagen
    public function getDuration(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    // Berechnet den Gesamtpreis
    public function calculateTotalPrice(): float
    {
        if (!$this->property) return 0;

        return $this->getDuration() * $this->property->price_per_night;
    }
}