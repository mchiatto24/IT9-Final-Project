<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'check_in',
        'check_out',
        'status',
        'guest_id',
        'room_id',
    ];

    // Cast check_in and check_out as datetime to automatically parse them as Carbon instances
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // The guest who made this booking
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // The room that was booked
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
