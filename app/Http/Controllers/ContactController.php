<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'required|string',
        ]);

        $apiKey = env('SENDGRID_API_KEY');

        $payload = [
            "personalizations" => [[
                "to" => [["email" => "illiashapshalov38@gmail.com"]],
            ]],
            "from" => ["email" => "illiashapshalov38@gmail.com"],
            "subject" => "[Contact Form] " . ($request->subject ?? 'No subject'),
            "content" => [[
                "type" => "text/html",
                "value" => "
                    <h3>New Contact Message</h3>
                    <p><b>Name:</b> {$request->name}</p>
                    <p><b>Email:</b> {$request->email}</p>
                    <p><b>Message:</b><br>{$request->message}</p>
                "
            ]]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json'
        ])->post('https://api.sendgrid.com/v3/mail/send', $payload);

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'status' => $response->status(),
            'body' => $response->body(),
        ], $response->status());
    }
}