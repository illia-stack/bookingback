<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'city',
        'price_per_night',
        'address',
        'image_url',
        'max_guests',
        'user_id',
    ];
}