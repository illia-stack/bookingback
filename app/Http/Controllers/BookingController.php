<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Neue Buchung erstellen + direkt Stripe Checkout starten
     */
    public function store(
        Request $request,
        BookingService $bookingService,
        PaymentService $paymentService
    ) {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            // 1. Booking erstellen (PENDING)
            $booking = $bookingService->createBooking(
                $userId,
                $request->property_id,
                $request->check_in,
                $request->check_out
            );

            // 2. Stripe Checkout Session erstellen
            $session = $paymentService->createCheckoutSession($booking);

            // 3. Optional: Status setzen (PROCESSING)
            $bookingService->markAsProcessing($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking created',
                'data' => $booking,
                'checkout_url' => $session->url
            ], 201);

        } catch (\Exception $e) {

            Log::error('Booking error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Booking failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Alle Bookings des Users
     */
    public function myBookings()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $bookings = Booking::with('property')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}