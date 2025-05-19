<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingLog extends Model
{
    protected $table = 'housekeeping_log';
    protected $primaryKey = 'hk_log_id';
    public $incrementing = true;
    protected $fillable = [
        'log_date',
        'status',
        'staff_id',
        'room_id',
    ];

    // The staff member who performed this task
    public function staff()
    {
        return $this->belongsTo(\App\Models\User::class, 'staff_id');
    }

    // The room that was cleaned
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}