<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Neue Buchung erstellen
     */
    public function store(Request $request, BookingService $bookingService)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        try {
            $booking = $bookingService->createBooking(
                auth()->id(),
                $request->property_id,
                $request->check_in,
                $request->check_out
            );

            return response()->json([
                'message' => 'Booking created',
                'success' => true,
                'data' => $booking
            ], 201);

        } catch (\Exception $e) {

            Log::error('Booking error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Booking failed',
                'error' => $e->getMessage(),
                'success' => false
            ], 400);
        }
    }

    /**
     * Alle Buchungen des Users
     */
    public function myBookings()
    {
        return response()->json(
            Booking::with('property')
                ->where('user_id', auth()->id())
                ->get()
        );
    }
}