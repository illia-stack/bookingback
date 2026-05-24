<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\User;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::truncate();
    // 1️⃣ Admin User sicherstellen
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'user', // optional
            ]
        );

        // 2️⃣ Properties erstellen
        $properties = [
            // Madrid
            [
                'title' => 'Modern Apartment in Madrid',
                'description' => 'Beautiful apartment in city center',
                'city' => 'Madrid',
                'address' => 'Gran Via 10',
                'price_per_night' => 120,
                'max_guests' => 2,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Madrid_1.jpg',
            ],
            [
                'title' => 'Charming Studio Madrid',
                'description' => 'Compact studio near Retiro Park',
                'city' => 'Madrid',
                'address' => 'Paseo del Prado 15',
                'price_per_night' => 80,
                'max_guests' => 2,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Madrid_2.jpg',
            ],
            [
                'title' => 'Luxury Flat Madrid',
                'description' => 'Spacious flat with balcony in city center',
                'city' => 'Madrid',
                'address' => 'Calle Mayor 22',
                'price_per_night' => 250,
                'max_guests' => 4,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Madrid_3.jpg',
            ],

            // Barcelona
            [
                'title' => 'Beach House Barcelona',
                'description' => 'Sea view house near beach',
                'city' => 'Barcelona',
                'address' => 'Beach Street 5',
                'price_per_night' => 200,
                'max_guests' => 4,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Barcelona_1.jpg',
            ],
            [
                'title' => 'Cozy Apartment Barcelona',
                'description' => 'Nice apartment close to Sagrada Familia',
                'city' => 'Barcelona',
                'address' => 'Carrer de Mallorca 200',
                'price_per_night' => 150,
                'max_guests' => 3,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Barcelona_2.jpg',
            ],
            [
                'title' => 'Seaside Villa Barcelona',
                'description' => 'Villa with private pool and sea view',
                'city' => 'Barcelona',
                'address' => 'Passeig Marítim 50',
                'price_per_night' => 300,
                'max_guests' => 6,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Barcelona_3.jpg',
            ],

            // Valencia
            [
                'title' => 'Cozy Loft Valencia',
                'description' => 'Modern loft in old town',
                'city' => 'Valencia',
                'address' => 'Old Town 3',
                'price_per_night' => 90,
                'max_guests' => 2,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Valencia_1.jpg',
            ],
            [
                'title' => 'Bright Loft Valencia',
                'description' => 'Modern loft with rooftop terrace',
                'city' => 'Valencia',
                'address' => 'Calle de la Paz 12',
                'price_per_night' => 100,
                'max_guests' => 2,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Valencia_2.jpg',
            ],
            [
                'title' => 'Family House Valencia',
                'description' => 'Spacious house near the beach',
                'city' => 'Valencia',
                'address' => 'Avenida del Mar 18',
                'price_per_night' => 220,
                'max_guests' => 5,
                'image_url' => 'https://jbaxvsvzbonlmdamzzzb.supabase.co/storage/v1/object/public/properties/Valencia_3.jpg',
            ],
        ];
        foreach ($properties as $property) {
            Property::updateOrCreate(
                ['title' => $property['title']], // sucht Eintrag
                array_merge($property, [
                    'user_id' => $user->id,
                ])
            );
        }
    }
}