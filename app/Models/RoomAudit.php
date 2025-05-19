<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAudit extends Model
{
    protected $fillable = [
        'room_id',
        'audit_date',
        'notes',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}