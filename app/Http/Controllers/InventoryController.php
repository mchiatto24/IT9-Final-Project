<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Report;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('supplier')->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $inventoryItems = $query->paginate(20);

        return view('inventory.index', compact('inventoryItems'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->pluck('name', 'id');
        return view('inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string',
            'category'           => 'required|string',
            'quantity'           => 'required|integer|min:0',
            'reorder_level'      => 'required|integer|min:0',
            'reorder_quantity'   => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'supplier_id'        => 'required|exists:suppliers,id',
        ]);

        $inventory = Inventory::create($data);

        // ✅ Log the initial addition
        if ($inventory->quantity > 0) {
            InventoryLog::create([
                'item_id'    => $inventory->id,
                'action'     => 'add',
                'change_qty' => $inventory->quantity,
                'staff_id'   => auth()->id(),
            ]);
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory item created.');
    }

    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::orderBy('name')->pluck('name', 'id');
        return view('inventory.edit', compact('inventory', 'suppliers'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'name'               => 'required|string',
            'category'           => 'required|string',
            'quantity'           => 'required|integer|min:0',
            'reorder_level'      => 'required|integer|min:0',
            'reorder_quantity'   => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'supplier_id'        => 'required|exists:suppliers,id',
        ]);

        $oldQty = $inventory->quantity;
        $inventory->update($data);

        // Log if quantity changed
        if ($oldQty !== $inventory->quantity) {
            InventoryLog::create([
                'item_id'    => $inventory->id,
                'action'     => 'adjust',
                'change_qty' => $inventory->quantity - $oldQty,
                'staff_id'   => auth()->id(),
            ]);
        }

        if ($inventory->quantity <= $inventory->reorder_level) {
            $this->createReport(
                $inventory,
                "This item ({$inventory->name}) is in need of reordering (qty: {$inventory->quantity} ≤ reorder level {$inventory->reorder_level})."
            );
        }

        if ($inventory->quantity <= $inventory->low_stock_threshold) {
            $this->createReport(
                $inventory,
                "This item ({$inventory->name}) is low in stock (qty: {$inventory->quantity} ≤ threshold {$inventory->low_stock_threshold})."
            );
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory updated.');
    }

    public function destroy(Inventory $inventory)
    {
        // Log the removal action
        InventoryLog::create([
            'item_id'    => $inventory->id,
            'action'     => 'remove',
            'change_qty' => $inventory->quantity,
            'staff_id'   => auth()->id(),
        ]);

        $inventory->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory item deleted.');
    }

    private function createReport(Inventory $item, string $message)
    {
        Report::create([
            'item_name' => $item->name,
            'message'   => $message,
        ]);
    }
}