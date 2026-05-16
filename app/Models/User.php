<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /*
    |--------------------------------------------------------------------------
    | HIDDEN FIELDS
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 12 automatische Hashing
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS HELPERS
    |--------------------------------------------------------------------------
    */

    public function hasBookings(): bool
    {
        return $this->bookings()->exists();
    }

    public function ownsProperty(int $propertyId): bool
    {
        return $this->properties()
            ->where('id', $propertyId)
            ->exists();
    }

    public function totalSpent(): float
    {
        return (float) $this->bookings()
            ->where('status', 'paid')
            ->sum('total_price');
    }
}