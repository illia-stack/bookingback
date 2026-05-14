<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminReportController extends Controller
{
    public function exportBookings(Request $request)
    {
        $token = $request->bearerToken();

        $response = Http::withToken($token)
            ->get('https://booking-report.onrender.com/api/reports/bookings');

        return response($response->body(), 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename=booking-report.xml');
    }
}