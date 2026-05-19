<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Madrid
        Property::create([
            'title' => 'Modern Apartment in Madrid',
            'description' => 'Beautiful apartment in city center',
            'city' => 'Madrid',
            'address' => 'Gran Via 10',
            'price_per_night' => 120,
            'max_guests' => 2,
            'image_url' => 'https://images.unsplash.com/photo-1.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Charming Studio Madrid',
            'description' => 'Compact studio near Retiro Park',
            'city' => 'Madrid',
            'address' => 'Paseo del Prado 15',
            'price_per_night' => 80,
            'max_guests' => 2,
            'image_url' => 'https://images.unsplash.com/photo-4.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Luxury Flat Madrid',
            'description' => 'Spacious flat with balcony in city center',
            'city' => 'Madrid',
            'address' => 'Calle Mayor 22',
            'price_per_night' => 250,
            'max_guests' => 4,
            'image_url' => 'https://images.unsplash.com/photo-5.jpg',
            'user_id' => 1
        ]);


        // Barcelona
        Property::create([
            'title' => 'Beach House Barcelona',
            'description' => 'Sea view house near beach',
            'city' => 'Barcelona',
            'address' => 'Beach Street 5',
            'price_per_night' => 200,
            'max_guests' => 4,
            'image_url' => 'https://images.unsplash.com/photo-2.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Cozy Apartment Barcelona',
            'description' => 'Nice apartment close to Sagrada Familia',
            'city' => 'Barcelona',
            'address' => 'Carrer de Mallorca 200',
            'price_per_night' => 150,
            'max_guests' => 3,
            'image_url' => 'https://images.unsplash.com/photo-6.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Seaside Villa Barcelona',
            'description' => 'Villa with private pool and sea view',
            'city' => 'Barcelona',
            'address' => 'Passeig Marítim 50',
            'price_per_night' => 300,
            'max_guests' => 6,
            'image_url' => 'https://images.unsplash.com/photo-7.jpg',
            'user_id' => 1
        ]);


        // Valencia
        Property::create([
            'title' => 'Cozy Loft Valencia',
            'description' => 'Modern loft in old town',
            'city' => 'Valencia',
            'address' => 'Old Town 3',
            'price_per_night' => 90,
            'max_guests' => 2,
            'image_url' => 'https://images.unsplash.com/photo-3.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Bright Loft Valencia',
            'description' => 'Modern loft with rooftop terrace',
            'city' => 'Valencia',
            'address' => 'Calle de la Paz 12',
            'price_per_night' => 100,
            'max_guests' => 2,
            'image_url' => 'https://images.unsplash.com/photo-8.jpg',
            'user_id' => 1
        ]);

        Property::create([
            'title' => 'Family House Valencia',
            'description' => 'Spacious house near the beach',
            'city' => 'Valencia',
            'address' => 'Avenida del Mar 18',
            'price_per_night' => 220,
            'max_guests' => 5,
            'image_url' => 'https://images.unsplash.com/photo-9.jpg',
            'user_id' => 1
        ]);
        
    }
}