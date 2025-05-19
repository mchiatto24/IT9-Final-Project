<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Inventory;
use App\Models\RoomInventory;
use Illuminate\Http\Request;

class RoomInventoryController extends Controller
{
    public function index()
    {
        $assignments = RoomInventory::with('room','item')
                          ->paginate(20);
        return view('room_inventory.index', compact('assignments'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_number')->pluck('room_number','id');
        $items = Inventory::orderBy('name')->pluck('name','id');
        return view('room_inventory.create', compact('rooms','items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'  => 'required|exists:rooms,id',
            'item_id'  => 'required|exists:inventory,id',
            'quantity' => 'required|integer|min:0',
        ]);

        RoomInventory::updateOrCreate(
            ['room_id'=>$data['room_id'],'item_id'=>$data['item_id']],
            ['quantity'=>$data['quantity']]
        );

        return redirect()->route('room-inventory.index')
                         ->with('success','Room inventory assigned.');
    }

    public function show(RoomInventory $roomInventory)
    {
        return view('room_inventory.show', compact('roomInventory'));
    }

    public function edit(RoomInventory $roomInventory)
    {
        $rooms = Room::orderBy('room_number')->pluck('room_number','id');
        $items = Inventory::orderBy('name')->pluck('name','id');
        return view('room_inventory.edit', compact('roomInventory','rooms','items'));
    }

    public function update(Request $request, RoomInventory $roomInventory)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $roomInventory->update($data);

        return redirect()->route('room-inventory.index')
                         ->with('success','Room inventory updated.');
    }

    public function destroy(RoomInventory $roomInventory)
    {
        $roomInventory->delete();

        return redirect()->route('room-inventory.index')
                         ->with('success','Assignment removed.');
    }
}