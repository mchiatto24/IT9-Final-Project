<?php

namespace App\Http\Controllers;

use App\Models\AssignedItem;
use App\Models\Room;
use App\Models\Inventory;
use App\Models\InventoryLog; // Add the import
use Illuminate\Http\Request;

class AssignedItemController extends Controller
{
    public function index()
    {
        $assignedItems = AssignedItem::with(['room', 'inventoryItem'])->get();
        return view('assigned_items.index', compact('assignedItems'));
    }

    public function create()
    {
        $rooms = Room::all();
        $inventoryItems = Inventory::where('quantity', '>', 0)->get();
        return view('assigned_items.create', compact('rooms', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'inventory_item_id' => 'required|exists:inventory,id',
            'quantity_assigned' => 'required|integer|min:1',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_item_id);

        if ($inventory->quantity < $request->quantity_assigned) {
            return back()->withErrors(['quantity_assigned' => 'Not enough stock available.']);
        }

        $inventory->decrement('quantity', $request->quantity_assigned);

        // ✅ Log the removal action
        InventoryLog::create([
            'item_id'    => $inventory->id,
            'action'     => 'remove',
            'change_qty' => $request->quantity_assigned,
            'staff_id'   => auth()->id(),
        ]);

        AssignedItem::create($request->all());

        return redirect()->route('assigned-items.index')->with('success', 'Item assigned to room successfully.');
    }

    public function edit($id)
    {
        // Retrieve the assigned item by ID
        $assignedItem = AssignedItem::findOrFail($id);

        // Fetch the available rooms and inventory items to display in the edit form
        $rooms = Room::all();
        $inventoryItems = Inventory::where('quantity', '>', 0)->get();

        // Return the view with the necessary data
        return view('assigned_items.edit', compact('assignedItem', 'rooms', 'inventoryItems'));
    }

    public function destroy(AssignedItem $assignedItem)
    {
        $assignedItem->inventoryItem->increment('quantity', $assignedItem->quantity_assigned);

        // ✅ Log the restoration action
        InventoryLog::create([
            'item_id'    => $assignedItem->inventoryItem->id,
            'action'     => 'add',
            'change_qty' => $assignedItem->quantity_assigned,
            'staff_id'   => auth()->id(),
        ]);

        $assignedItem->delete();

        return redirect()->route('assigned-items.index')->with('success', 'Assignment removed and inventory restored.');
    }

    public function update(Request $request, AssignedItem $assignedItem)
    {
        $validated = $request->validate([
            'room_id'            => 'required|exists:rooms,id',
            'inventory_item_id'  => 'required|exists:inventory,id',
            'quantity_assigned'  => 'required|integer|min:1',
        ]);

        $oldQty    = $assignedItem->quantity_assigned;
        $newItemId = $validated['inventory_item_id'];
        $newQty    = $validated['quantity_assigned'];

        if ($assignedItem->inventory_item_id != $newItemId) {
            Inventory::find($assignedItem->inventory_item_id)->increment('quantity', $oldQty);
            Inventory::findOrFail($newItemId)->decrement('quantity', $newQty);
        } else {
            $delta = $newQty - $oldQty;
            if ($delta > 0) {
                Inventory::findOrFail($newItemId)->decrement('quantity', $delta);
            } elseif ($delta < 0) {
                Inventory::findOrFail($newItemId)->increment('quantity', -$delta);
            }
        }

        // ✅ Log the adjustment action
        InventoryLog::create([
            'item_id'    => $assignedItem->inventoryItem->id,
            'action'     => 'adjust',
            'change_qty' => $newQty - $oldQty,
            'staff_id'   => auth()->id(),
        ]);

        $assignedItem->update($validated);

        return redirect()->route('assigned-items.index')->with('success', 'Assignment updated and inventory stock adjusted.');
    }
}