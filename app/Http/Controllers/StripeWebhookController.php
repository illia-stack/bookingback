<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();

        $sigHeader = $request->header('Stripe-Signature');

        $secret = config('services.stripe.webhook_secret');

        try {

            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );

        } catch (\Exception $e) {

            Log::error('Stripe webhook signature error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Invalid payload'
            ], 400);
        }

        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $bookingId = $session->metadata->booking_id ?? null;

            if (!$bookingId || !is_numeric($bookingId)) {

                return response()->json([
                    'error' => 'Invalid booking id'
                ], 400);
            }

            $booking = Booking::find($bookingId);

            if (!$booking) {

                return response()->json([
                    'error' => 'Booking not found'
                ], 404);
            }

            // Stripe sendet Events mehrfach
            if ($booking->status === BookingStatus::PAID) {

                return response()->json([
                    'status' => 'already processed'
                ], 200);
            }

            DB::transaction(function () use ($booking, $session) {

                $booking->update([
                    'status' => BookingStatus::PAID,
                    'paid_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent ?? null,
                ]);

            });
        }

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}