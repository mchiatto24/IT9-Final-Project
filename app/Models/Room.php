<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'room_rate',
        'room_status',
        'audited'
    ];

    // Bookings for this room
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Housekeeping logs for this room
    public function housekeepingLogs()
    {
        return $this->hasMany(HousekeepingLog::class, 'room_id');
    }

    public function assignedItems()
    {
        return $this->hasMany(AssignedItem::class);
    }

    // Inventory items assigned to this room (pivot)
    public function inventoryItems()
    {
        return $this->belongsToMany(
            Inventory::class,
            'room_inventory',
            'room_id',
            'item_id'
        )->withPivot('quantity')
         ->withTimestamps();
    }
}