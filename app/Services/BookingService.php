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
     */
    public function isAvailable($propertyId, $checkIn, $checkOut)
    {
        return !Booking::where('property_id', $propertyId)
            ->whereIn('status', [
                BookingStatus::PENDING,
                BookingStatus::PROCESSING,
                BookingStatus::PAID,
            ])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
            })
            ->exists();
    }

    /**
     * Preis berechnen
     */
    public function calculatePrice(Property $property, Carbon $checkIn, Carbon $checkOut)
    {
        $days = max(1, $checkIn->diffInDays($checkOut));

        return $days * $property->price_per_night;
    }

    /**
     * Booking erstellen (PENDING)
     */
    public function createBooking($userId, $propertyId, $checkIn, $checkOut)
    {
        if (!$userId) {
            throw new \Exception('Unauthorized');
        }

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        if ($checkInDate->greaterThanOrEqualTo($checkOutDate)) {
            throw new \Exception('Invalid dates');
        }

        

        return DB::transaction(function () use (
            $userId,
            $propertyId,
            $checkInDate,
            $checkOutDate,
        ) {
            $property = Property::lockForUpdate()->findOrFail($propertyId);
            $totalPrice = $this->calculatePrice(
                $property,
                $checkInDate,
                $checkOutDate
            );
            // LOCK rows
            $conflict = Booking::where('property_id', $propertyId)
                ->whereIn('status', [
                    BookingStatus::PENDING,
                    BookingStatus::PROCESSING,
                    BookingStatus::PAID,
                ])
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->where('check_in', '<', $checkOutDate)
                        ->where('check_out', '>', $checkInDate);
                })
                ->lockForUpdate()
                ->first();

            if ($conflict) {
                throw new \Exception('Property not available');
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
     */
    public function markAsProcessing(Booking $booking)
    {
        $booking->update([
            'status' => BookingStatus::PROCESSING,
        ]);

        return $booking;
    }

    /**
     * Status auf Paid setzen (Webhook)
     */
    public function markAsPaid(Booking $booking, $stripePaymentIntentId = null)
    {
        $booking->update([
            'status' => BookingStatus::PAID,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $stripePaymentIntentId,
        ]);

        return $booking;
    }
}