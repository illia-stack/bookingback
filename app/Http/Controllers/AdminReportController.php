<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminReportController extends Controller
{
    public function exportBookings(Request $request)
    {
        $token = $request->bearerToken();

        // Spring Boot API aufrufen
        $response = Http::withToken($token)
            ->get('https://booking-report.onrender.com/report/excel');

        return response($response->body(), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="booking-report.xlsx"')
            ->header('Content-Length', strlen($response->body()))
            ->header('Content-Transfer-Encoding', 'binary');
    }
}