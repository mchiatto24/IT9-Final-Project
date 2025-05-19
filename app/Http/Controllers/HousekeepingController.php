<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingLog;
use App\Models\Room;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function index()
    {
        $logs = HousekeepingLog::with('room','staff')
                 ->orderBy('log_date','desc')
                 ->paginate(20);
        return view('housekeeping.index', compact('logs'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_number')->pluck('room_number','id');
        return view('housekeeping.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'  => 'required|exists:rooms,id',
            'status'   => 'required|in:dirty,in-progress,clean',
        ]);

        // staff_id from logged-in user
        $data['staff_id'] = auth()->id();

        HousekeepingLog::create($data);

        return redirect()->route('housekeeping.index')
                         ->with('success','Housekeeping log added.');
    }

    public function show(HousekeepingLog $housekeeping)
    {
        return view('housekeeping.show', ['log'=>$housekeeping]);
    }

    public function edit(HousekeepingLog $housekeeping)
    {
        $rooms = Room::orderBy('room_number')->pluck('room_number','id');
        return view('housekeeping.edit', compact('housekeeping','rooms'));
    }

    public function update(Request $request, HousekeepingLog $housekeeping)
    {
        $data = $request->validate([
            'room_id'  => 'required|exists:rooms,id',
            'status'   => 'required|in:dirty,in-progress,clean',
        ]);

        $housekeeping->update($data);

        return redirect()->route('housekeeping.index')
                         ->with('success','Housekeeping log updated.');
    }

    public function destroy(HousekeepingLog $housekeeping)
    {
        $housekeeping->delete();

        return redirect()->route('housekeeping.index')
                         ->with('success','Housekeeping log deleted.');
    }
}