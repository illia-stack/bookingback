<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function exportBookings(Request $request)
    {
        // Schritt 1: Bearer Token auslesen
        $token = $request->bearerToken();
        Log::info('ExportBookings: Bearer Token erhalten', ['token' => substr($token, 0, 10) . '...']);

        // Schritt 2: Spring Boot API aufrufen
        $springUrl = 'https://booking-report.onrender.com/report/excel';
        Log::info('ExportBookings: Aufruf Spring Boot API', ['url' => $springUrl]);

        $response = Http::withToken($token)
            ->timeout(30) // Timeout setzen, falls große Excel-Datei
            ->get($springUrl);

        // Schritt 3: Debug Infos loggen
        Log::info('ExportBookings: Spring Response Status', ['status' => $response->status()]);
        Log::info('ExportBookings: Content-Type Spring Response', ['content_type' => $response->header('Content-Type')]);
        Log::info('ExportBookings: Body length', ['length' => strlen($response->body())]);

        // Schritt 4: Prüfen, ob Response OK ist und Excel-Daten enthält
        if ($response->failed()) {
            Log::error('ExportBookings: Spring Boot API Fehler', ['body' => $response->body()]);
            return response()->json([
                'error' => 'Fehler beim Abrufen des Excel-Reports',
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500) // nur Preview im Log/Debug
            ], 500);
        }

        // Schritt 5: Excel als Stream zurückgeben
        return new StreamedResponse(function () use ($response) {
            echo $response->body();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="booking-report.xlsx"',
            'Content-Length' => strlen($response->body()),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}