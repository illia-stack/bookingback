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
     * Neue Buchung erstellen + Stripe Checkout starten
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
            'locale' => 'nullable|string',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            // 1️⃣ Booking erstellen (PENDING)
            $booking = $bookingService->createBooking(
                $userId,
                $request->property_id,
                $request->check_in,
                $request->check_out
            );

            // 2️⃣ Stripe Checkout Session erstellen
            $session = $paymentService->createCheckoutSession(
                $booking,
                $request->input('locale', 'auto') // ✅ korrekt
            );

            // 3️⃣ Optional: Status auf PROCESSING setzen
            $bookingService->markAsProcessing($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking' => $booking,
                    'checkout_url' => $session->url
                ]
            ], 201);

        } catch (\Exception $e) {

            Log::error('Booking creation error', [
                'message' => $e->getMessage(),
                'user' => $userId,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Alle Bookings des aktuellen Users
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
            'message' => $bookings->isEmpty() ? 'No bookings found' : 'Bookings retrieved successfully',
            'data' => $bookings
        ]);
    }
}