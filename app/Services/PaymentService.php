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

    public function createCheckoutSession(
        Booking $booking,
        string $locale = 'auto'
    )
    {
        if (!$booking) {
            throw new \Exception('Booking not found');
        }

        $frontendUrl = config('app.frontend_url');

        if (!$frontendUrl) {
            throw new \Exception('Frontend URL not configured');
        }

        $session = Session::create(
            [
                'payment_method_types' => ['card'],

                'locale' => $locale,

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

               'success_url' => $frontendUrl . '/success?booking_id=' . $booking->id . '&lang=' . $locale,
               'cancel_url' => $frontendUrl . '/cancel?booking_id=' . $booking->id . '&lang=' . $locale,

                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ],

            // ✅ STRIPE OPTIONS ARRAY
            [
                'idempotency_key' => 'booking_' . $booking->id,
            ]
        );

        $booking->update([
            'stripe_session_id' => $session->id,
            'status' => BookingStatus::PROCESSING,
        ]);

        return $session; 
    }
}