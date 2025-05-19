<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'reorder_level',      // ✅ Corrected field name
        'low_stock_threshold',
        'reorder_quantity',
        'supplier_id',
    ];

    /**
     * The supplier that provides this item.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Log entries for this item.
     */
    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'item_id');
    }

    public function assignedItems()
    {
        return $this->hasMany(AssignedItem::class, 'inventory_item_id');
    }

    /**
     * Rooms this item is assigned to.
     */
    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'room_inventory',
            'item_id',
            'room_id'
        )->withPivot('quantity')
         ->withTimestamps();
    }
}