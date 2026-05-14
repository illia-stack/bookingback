<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminReportController extends Controller
{
    public function exportBookings(Request $request)
    {
        // Bearer Token vom Frontend
        $token = $request->bearerToken();

        // Aufruf der Spring Boot API, die Excel liefert
        $response = Http::withToken($token)
            ->get('https://booking-report.onrender.com/api/reports/bookings');

        // Rückgabe als Download
        return response($response->body(), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename=booking-report.xlsx');
    }
}