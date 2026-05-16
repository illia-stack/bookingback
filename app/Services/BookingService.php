<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use App\Enums\BookingStatus;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Prüfen ob Property im Zeitraum verfügbar ist
     *
     * @param int $propertyId
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @return bool
     */
    public function isAvailable(int $propertyId, $checkIn, $checkOut): bool
    {
        $checkIn = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
        $checkOut = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);

        return !Booking::where('property_id', $propertyId)
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
            })
            ->exists();
    }

    /**
     * Preis berechnen
     *
     * @param Property $property
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @return float
     */
    public function calculatePrice(Property $property, $checkIn, $checkOut): float
    {
        $checkIn = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
        $checkOut = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);

        $days = max(1, $checkIn->diffInDays($checkOut));

        return $days * $property->price_per_night;
    }

    /**
     * Booking erstellen (PENDING)
     *
     * @param int $userId
     * @param int $propertyId
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @return Booking
     * @throws \Exception
     */
    public function createBooking(int $userId, int $propertyId, $checkIn, $checkOut): Booking
    {
        if (!$userId) {
            throw new \Exception('Unauthorized');
        }

        $checkInDate = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
        $checkOutDate = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);

        if ($checkInDate->greaterThanOrEqualTo($checkOutDate)) {
            throw new \Exception('Invalid dates');
        }

        $property = Property::findOrFail($propertyId);

        $totalPrice = $this->calculatePrice($property, $checkInDate, $checkOutDate);

        return DB::transaction(function () use ($userId, $propertyId, $checkInDate, $checkOutDate, $totalPrice) {

            // Double booking protection
            if (!$this->isAvailable($propertyId, $checkInDate, $checkOutDate)) {
                throw new \Exception('Property not available for selected dates');
            }

            return Booking::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'check_in' => $checkInDate,
                'check_out' => $checkOutDate,
                'total_price' => $totalPrice,
                'status' => BookingStatus::PENDING,
            ]);
        });
    }

    /**
     * Status auf Processing setzen (Stripe Checkout gestartet)
     *
     * @param Booking $booking
     * @return Booking
     */
    public function markAsProcessing(Booking $booking): Booking
    {
        $booking->update([
            'status' => BookingStatus::PROCESSING,
        ]);

        return $booking;
    }

    /**
     * Status auf Paid setzen (Webhook)
     *
     * @param Booking $booking
     * @param string|null $stripePaymentIntentId
     * @return Booking
     */
    public function markAsPaid(Booking $booking, ?string $stripePaymentIntentId = null): Booking
    {
        $booking->update([
            'status' => BookingStatus::PAID,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $stripePaymentIntentId,
        ]);

        return $booking;
    }
}