<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoomInventory extends Pivot
{
    protected $table = 'room_inventory';
    protected $fillable = [
        'room_id',
        'item_id',
        'quantity',
    ];
}