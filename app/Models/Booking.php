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

    protected $casts = [
        'status' => BookingStatus::class,
        'check_in' => 'date',
        'check_out' => 'date',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function isPaid(): bool
    {
        return $this->status === BookingStatus::PAID;
    }
}