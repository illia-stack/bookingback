<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. DEMO USER (für Login + Ownership)
        |--------------------------------------------------------------------------
        */

        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. PROPERTIES (Testdaten für UI + Booking Flow)
        |--------------------------------------------------------------------------
        */

        $properties = [
            [
                'title' => 'Modern Apartment in Madrid',
                'description' => 'Beautiful apartment in city center',
                'city' => 'Madrid',
                'address' => 'Gran Via 10',
                'price_per_night' => 120,
                'max_guests' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1560448204-603b3fc33ddc',
            ],
            [
                'title' => 'Beach House Barcelona',
                'description' => 'Sea view house near beach',
                'city' => 'Barcelona',
                'address' => 'Beach Street 5',
                'price_per_night' => 200,
                'max_guests' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511',
            ],
            [
                'title' => 'Cozy Loft Valencia',
                'description' => 'Modern loft in old town',
                'city' => 'Valencia',
                'address' => 'Old Town 3',
                'price_per_night' => 90,
                'max_guests' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267',
            ],
        ];

        foreach ($properties as $property) {
            Property::firstOrCreate(
                ['title' => $property['title']],
                array_merge($property, [
                    'user_id' => $user->id,
                ])
            );
        }
    }
}