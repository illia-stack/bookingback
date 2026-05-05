<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
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
            'title' => 'Cozy Loft Valencia',
            'description' => 'Modern loft in old town',
            'city' => 'Valencia',
            'address' => 'Old Town 3',
            'price_per_night' => 90,
            'max_guests' => 2,
            'image_url' => 'https://images.unsplash.com/photo-3.jpg',
            'user_id' => 1
        ]);
    }
}