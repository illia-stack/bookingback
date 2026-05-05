<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Booking;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking)
    {
        $session = Session::create([
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

            'success_url' => config('app.frontend_url') . '/success?booking_id=' . $booking->id,
            'cancel_url' => config('app.frontend_url') . '/cancel?booking_id=' . $booking->id,

            'metadata' => [
                'booking_id' => $booking->id,
            ],
        ]);

        // speichern für später (Webhook)
        $booking->update([
            'stripe_session_id' => $session->id,
        ]);

        return $session;
    }
}