<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Booking;

class Property extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'title',
        'description',
        'city',
        'price_per_night',
        'address',
        'image_url',
        'max_guests',
        'user_id',
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS HELPERS
    |--------------------------------------------------------------------------
    */

    // Gibt den Preis pro Nacht zurück (typisiert)
    public function averagePricePerDay(): float
    {
        return (float) $this->price_per_night;
    }

    // Prüft, ob diese Property einem bestimmten User gehört
    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    // Prüft, ob aktive Buchungen bestehen
    public function hasActiveBookings(): bool
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'processing', 'paid'])
            ->exists();
    }
}