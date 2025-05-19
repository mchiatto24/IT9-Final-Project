<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Inventory;
use App\Models\AssignedItem;  // Make sure to import AssignedItem model
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryLog::with('item', 'staff')->orderBy('log_date', 'desc');

        // Filtering logic for 'item_id'
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Filtering logic for 'action'
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtering by Date Range (From Date and To Date)
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('log_date', [$request->from, $request->to]);
        } elseif ($request->filled('from')) {
            $query->where('log_date', '>=', $request->from);
        } elseif ($request->filled('to')) {
            $query->where('log_date', '<=', $request->to);
        }

        // Fetch logs based on applied filters
        $logs = $query->paginate(20); // You can adjust the pagination as needed

        // Fetch all items to populate the filter dropdown
        $items = Inventory::orderBy('name')->get();

        return view('inventory_logs.index', compact('logs', 'items'));
    }

    public function create()
    {
        $items = Inventory::orderBy('name')->pluck('name', 'id');
        return view('inventory_logs.create', compact('items'));
    }

    public function store(Request $request)
    {
        // Validation for inventory assignment
        $request->validate([
            'room_id'            => 'required|exists:rooms,id',
            'inventory_item_id'  => 'required|exists:inventory,id',
            'quantity_assigned'  => 'required|integer|min:1',
        ]);

        // Reduce inventory after assignment
        $item = Inventory::findOrFail($request->inventory_item_id);
        $item->decrement('quantity', $request->quantity_assigned);

        // Create assigned record
        AssignedItem::create([
            'room_id'            => $request->room_id,
            'inventory_item_id'  => $request->inventory_item_id,
            'quantity_assigned'  => $request->quantity_assigned,
        ]);

        // ✅ Create inventory log for removal
        InventoryLog::create([
            'item_id'    => $item->id,
            'action'     => 'remove',  // This can be 'add', 'remove', or 'adjust' depending on action
            'change_qty' => $request->quantity_assigned,
            'staff_id'   => auth()->id(),
        ]);

        return redirect()->route('inventory-logs.index')
                         ->with('success', 'Item assigned and inventory log recorded successfully.');
    }

    public function show(InventoryLog $inventoryLog)
    {
        return view('inventory_logs.show', compact('inventoryLog'));
    }

    public function edit(InventoryLog $inventoryLog)
    {
        $items = Inventory::orderBy('name')->pluck('name', 'id');
        return view('inventory_logs.edit', compact('inventoryLog', 'items'));
    }

    public function update(Request $request, InventoryLog $inventoryLog)
    {
        $data = $request->validate([
            'item_id'    => 'required|exists:inventory,id',
            'action'     => 'required|in:add,remove,adjust',
            'change_qty' => 'required|integer|min:1',
        ]);

        // For simplicity, we won’t re-adjust the stock here.
        $inventoryLog->update($data);

        return redirect()->route('inventory-logs.index')
                         ->with('success', 'Inventory log updated.');
    }

    public function destroy(InventoryLog $inventoryLog)
    {
        $inventoryLog->delete();

        return redirect()->route('inventory-logs.index')
                         ->with('success', 'Inventory log deleted.');
    }
}
