<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * 📦 Alle Properties holen (mit Filter)
     */
    public function index(Request $request)
    {
        $query = Property::query();

        // 🔍 Stadt
        if ($city = $request->input('city')) {
            $query->where('city', $city);
        }

        // 💰 Preis
        if ($minPrice = $request->input('min_price')) {
            $query->where('price_per_night', '>=', $minPrice);
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->where('price_per_night', '<=', $maxPrice);
        }

        // 👥 Gäste
        if ($guests = $request->input('guests')) {
            $query->where('max_guests', '>=', $guests);
        }

        // Nur eigene Properties
        if ($request->filled('my') && auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        return response()->json([
            'success' => true,
            'data' => $query->latest()->paginate(10)
        ]);
    }

    /**
     * 🔍 Einzelne Property anzeigen
     */
    public function show($id)
    {
        $property = Property::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $property
        ]);
    }

    /**
     * ➕ Neue Property erstellen
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'image_url' => 'nullable|url|max:2048'
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $property = Property::create(array_merge(
            $request->only([
                'title',
                'description',
                'city',
                'address',
                'price_per_night',
                'max_guests',
                'image_url'
            ]),
            ['user_id' => $user->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully',
            'data' => $property
        ], 201);
    }

    /**
     * ✏️ Property aktualisieren
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $user = auth()->user();
        if ($property->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'price_per_night' => 'sometimes|required|numeric|min:0',
            'max_guests' => 'sometimes|required|integer|min:1',
            'image_url' => 'nullable|url|max:2048'
        ]);

        $property->update($request->only([
            'title',
            'description',
            'city',
            'address',
            'price_per_night',
            'max_guests',
            'image_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Property updated',
            'data' => $property
        ]);
    }

    /**
     * ❌ Property löschen
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        $user = auth()->user();
        if ($property->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property deleted'
        ]);
    }
}