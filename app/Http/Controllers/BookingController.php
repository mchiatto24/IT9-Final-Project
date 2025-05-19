<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['guest','room'])
                           ->orderBy('check_in','desc')
                           ->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $guests = Guest::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                       ->orderBy('first_name')
                       ->pluck('name', 'id');

        $rooms = Room::where('room_status','available')
                     ->orderBy('room_number')
                     ->pluck('room_number','id');

        return view('bookings.create', compact('guests','rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'guest_id'  => 'required|exists:guests,id',
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status'    => 'required|in:pending,confirmed,checked_in,checked_out,canceled',
        ]);

        // simplest overlap check
        $overlap = Booking::where('room_id', $data['room_id'])
            ->where(function($q) use($data) {
                $q->whereBetween('check_in',  [$data['check_in'], $data['check_out']])
                  ->orWhereBetween('check_out', [$data['check_in'], $data['check_out']]);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['room_id'=>'Room not available for those dates.'])->withInput();
        }

        // create booking
        $booking = Booking::create($data);

        // mark room occupied
        $room = Room::find($data['room_id']);
        $room->room_status = 'occupied';
        $room->save();

        return redirect()->route('bookings.index')
                         ->with('success','Booking created and room marked occupied.');
    }

    public function show(Booking $booking)
    {
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $guests = Guest::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                       ->orderBy('first_name')
                       ->pluck('name', 'id');

        $rooms = Room::orderBy('room_number')
                     ->pluck('room_number','id');

        return view('bookings.edit', compact('booking','guests','rooms'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'guest_id'  => 'required|exists:guests,id',
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status'    => 'required|in:pending,confirmed,checked_in,checked_out,canceled',
        ]);

        // if room changed, reset old room -> available, new room -> occupied
        if ($booking->room_id != $data['room_id']) {
            Room::where('id', $booking->room_id)
                ->update(['room_status' => 'available']);
            Room::where('id', $data['room_id'])
                ->update(['room_status' => 'occupied']);
        }

        $booking->update($data);

        // if booking is now checked_out or canceled, free up room
        if (in_array($data['status'], ['checked_out','canceled'])) {
            $booking->room->update(['room_status' => 'available']);
        }

        return redirect()->route('dashboard')
                         ->with('success','Booking updated and room status adjusted.');
    }

    public function destroy(Booking $booking)
    {
        // before deleting, if booking was active, free the room
        if (! in_array($booking->status, ['checked_out','canceled'])) {
            $booking->room->update(['room_status' => 'available']);
        }

        $booking->delete();

        return redirect()->route('bookings.index')
                         ->with('success','Booking deleted and room freed.');
    }
}