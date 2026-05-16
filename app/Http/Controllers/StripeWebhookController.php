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
    /**
     * Handle Stripe webhook events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature error', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid payload',
            ], 400);
        }

        switch ($event->type) {

            case 'checkout.session.completed':
                $session = $event->data->object;

                $bookingId = $session->metadata->booking_id ?? null;

                if (!$bookingId || !is_numeric($bookingId)) {
                    Log::warning('Stripe webhook: invalid booking id', [
                        'metadata' => $session->metadata,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid booking id',
                    ], 400);
                }

                $booking = Booking::find($bookingId);

                if (!$booking) {
                    Log::warning("Stripe webhook: booking not found", [
                        'booking_id' => $bookingId,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Booking not found',
                    ], 404);
                }

                // Stripe kann Events mehrfach senden
                if ($booking->status === BookingStatus::PAID) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Booking already processed',
                    ], 200);
                }

                // Update booking in a transaction
                DB::transaction(function () use ($booking, $session) {
                    $booking->update([
                        'status' => BookingStatus::PAID,
                        'paid_at' => now(),
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    ]);
                });

                Log::info("Stripe payment completed for booking {$booking->id}");

                break;

            default:
                // Ignoriere andere Events
                Log::info("Stripe webhook ignored event: {$event->type}");
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook handled successfully',
        ], 200);
    }
}