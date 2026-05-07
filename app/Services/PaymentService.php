<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Booking;
use App\Enums\BookingStatus;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking)
    {
        // Sicherheit: Booking muss existieren
        if (!$booking) {
            throw new \Exception('Booking not found');
        }

        $frontendUrl = config('app.frontend_url');

        if (!$frontendUrl) {
            throw new \Exception('Frontend URL not configured');
        }

        $session = Session::create([
            // verhindert doppelte Stripe Sessions
            'idempotency_key' => 'booking_' . $booking->id,

            'payment_method_types' => ['card'],

            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Booking #' . $booking->id,
                    ],
                    'unit_amount' => (int) round($booking->total_price * 100),
                ],
                'quantity' => 1,
            ]],

            'mode' => 'payment',

            'success_url' => $frontendUrl . '/success?booking_id=' . $booking->id,
            'cancel_url'  => $frontendUrl . '/cancel?booking_id=' . $booking->id,

            'metadata' => [
                'booking_id' => $booking->id,
            ],
        ]);

        // Booking in "processing" setzen → verhindert Doppel-Checkout
        $booking->update([
            'stripe_session_id' => $session->id,
            'status' => BookingStatus::PROCESSING ?? BookingStatus::PENDING,
        ]);

        return $session;
    }
}