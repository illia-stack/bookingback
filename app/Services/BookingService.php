<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use App\Enums\BookingStatus;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function isAvailable($propertyId, $checkIn, $checkOut)
    {
        return !Booking::where('property_id', $propertyId)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })
            ->exists();
    }

    public function calculatePrice($property, $checkIn, $checkOut)
    {
        $days = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));

        return $days * $property->price_per_night;
    }

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

        $property = Property::findOrFail($propertyId);

        $totalPrice = $this->calculatePrice($property, $checkInDate, $checkOutDate);

        return DB::transaction(function () use (
            $userId,
            $propertyId,
            $checkInDate,
            $checkOutDate,
            $totalPrice
        ) {
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
}