<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_info',
    ];

    // Items this supplier provides
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'supplier_id');
    }
}