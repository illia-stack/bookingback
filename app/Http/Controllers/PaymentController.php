<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\PaymentService;
use App\Enums\BookingStatus;

class PaymentController extends Controller
{
    /**
     * Start a checkout session for a booking.
     */
    public function checkout(Request $request, PaymentService $paymentService)
    {
        // Validate request
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        // Find booking for the current user
        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => __('Booking not found'),
            ], 404);
        }

        // Ensure booking is still pending
        if ($booking->status !== BookingStatus::PENDING) {
            return response()->json([
                'success' => false,
                'message' => __('Booking already processed'),
            ], 400);
        }

        try {
            // Create Stripe Checkout session
            $session = $paymentService->createCheckoutSession($booking);

            return response()->json([
                'success' => true,
                'checkout_url' => $session->url, // ✅ matches frontend PropertyDetail.jsx
            ]);

        } catch (\Exception $e) {

            // Log error for debugging
            \Log::error("Payment session failed for booking {$booking->id}: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => __('Payment session failed'),
            ], 500);
        }
    }
}