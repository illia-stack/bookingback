<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\PaymentService;
use App\Enums\BookingStatus;

class PaymentController extends Controller
{
    public function checkout(Request $request, PaymentService $paymentService)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found',
                'success' => false
            ], 404);
        }

        if ($booking->status !== BookingStatus::PENDING) {
            return response()->json([
                'message' => 'Booking already processed',
                'success' => false
            ], 400);
        }

        try {

            $session = $paymentService->createCheckoutSession($booking);

            return response()->json([
                'success' => true,
                'url' => $session->url
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Payment session failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}