<?php

namespace App\Http\Controllers;

use App\Models\RoomAudit;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomAuditController extends Controller
{
    public function index()
    {
        $audits = RoomAudit::with('room')->orderBy('audit_date', 'desc')->paginate(10);
        return view('room_audits.index', compact('audits'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('room_audits.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'audit_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        RoomAudit::create($data);

        return redirect()->route('room_audits.index')
                         ->with('success', 'Room audit record added.');
    }

    public function edit(RoomAudit $roomAudit)
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('room_audits.edit', compact('roomAudit', 'rooms'));
    }

    public function update(Request $request, RoomAudit $roomAudit)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'audit_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $roomAudit->update($data);

        return redirect()->route('room_audits.index')
                         ->with('success', 'Room audit record updated.');
    }

    public function destroy(RoomAudit $roomAudit)
    {
        $roomAudit->delete();

        return redirect()->route('room_audits.index')
                         ->with('success', 'Room audit record deleted.');
    }
}
