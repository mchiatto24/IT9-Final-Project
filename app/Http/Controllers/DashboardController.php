<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\Booking;
use App\Models\RoomAudit; // Make sure this is imported
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 0. Inventory Stats
        $inventoryCount = Inventory::count();
        $lowStockCount = Inventory::whereColumn('quantity', '<=', 'reorder_level')->count();

        // 1. Rooms Audited - Ensure RoomAudit is imported and used here
        $roomsAudited = RoomAudit::distinct('room_id')->count('room_id');

        // 2. Your existing stats queries
        $roomStats = Room::selectRaw('
            COUNT(*) as total_rooms,
            SUM(audited) as audited_count,
            SUM(room_status = "available") as available_count,
            SUM(room_status = "occupied") as occupied_count
        ')->first();

        $bookingStats = Booking::selectRaw('
            COUNT(*) as checkins_today,
            (SELECT COUNT(*) FROM bookings WHERE DATE(check_out) = CURDATE()) as checkouts_today
        ')->whereDate('check_in', today())
         ->first();

        // 3. Build the rooms query, with optional search filter
        $roomQuery = Room::orderBy('room_number');

        if ($search = $request->input('search')) {
            $roomQuery->where(function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('room_type', 'like',   "%{$search}%");
            });
        }

        // 4. Eager-load, paginate, and preserve the search term in links
        $rooms = $roomQuery
            ->with('inventoryItems') // Ensure this relationship is defined in the Room model
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 5. Return the view with all your statistics + filtered rooms
        return view('dashboard', [
            // Inventory Stats
            'inventoryCount'    => $inventoryCount,
            'lowStockCount'     => $lowStockCount,

            // Supplier Stats
            'suppliersCount'    => Supplier::count(),

            // Room Stats
            'roomsAuditedCount' => $roomsAudited, // Fixed this to use the correct variable
            'availableRooms'    => $roomStats->available_count ?? 0,
            'occupancyRate'     => $roomStats->total_rooms
                                  ? round(($roomStats->occupied_count / $roomStats->total_rooms) * 100, 2)
                                  : 0,

            // Booking Stats
            'checkinsToday'     => $bookingStats->checkins_today ?? 0,
            'checkoutsToday'    => $bookingStats->checkouts_today ?? 0,

            // Room List (with search)
            'rooms'             => $rooms,
        ]);
    }
}