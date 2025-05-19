<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $table = 'inventory_log';
    protected $primaryKey = 'log_id';
    public $incrementing = true;
    protected $fillable = [
        'action',
        'change_qty',
        'log_date',
        'item_id',
        'staff_id',
    ];

    // A single inventory item
    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id');
    }

    // The staff member who performed it
    public function staff()
    {
        return $this->belongsTo(\App\Models\User::class, 'staff_id');
    }
}