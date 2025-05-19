<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedItem extends Model
{
    protected $fillable = [
        'room_id',
        'inventory_item_id',
        'quantity_assigned',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(Inventory::class, 'inventory_item_id');
    }
}