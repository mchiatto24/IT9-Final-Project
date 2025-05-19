<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    // Table name matches `guests`, so no need for $table
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    // A guest can have many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}