<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::orderBy('room_number');

        if ($search = $request->input('search')) {
            $query->where('room_number', 'like', "%{$search}%")
                ->orWhere('room_type', 'like', "%{$search}%");
        }

        // paginate 10 per page (change to whatever makes sense),
        // and keep the ?search= in your links
        $rooms = $query->paginate(10)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number',
            'room_type'   => 'required|string',
            'room_rate'   => 'required|numeric',
            'room_status' => 'required|in:available,occupied,maintenance',
        ]);

        Room::create($data);

        return redirect()->route('rooms.index')
                         ->with('success', 'Room created.');
    }

    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'room_number' => "required|string|unique:rooms,room_number,{$room->id}",
            'room_type'   => 'required|string',
            'room_rate'   => 'required|numeric',
            'room_status' => 'required|in:available,occupied,maintenance',
        ]);

        $room->update($data);

        return redirect()->route('rooms.index')
                         ->with('success', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')
                         ->with('success', 'Room deleted.');
    }
}