<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Step 1: Retrieve IDs of reports already acknowledged from the session
        $ack = $request->session()->get('acknowledged_reports', []);

        // Step 2: Query low-stock and reorder alerts, excluding acknowledged ones
        $query = Inventory::whereColumn('quantity', '<=', 'reorder_level')
            ->orWhereColumn('quantity', '<=', 'low_stock_threshold');

        // Exclude already acknowledged reports by checking their IDs
        if (!empty($ack)) {
            $query->whereNotIn('id', $ack);
        }

        // Step 3: Fetch the low-stock items, ordered by quantity, and paginate
        $lowStockItems = $query
            ->orderBy('quantity', 'asc')
            ->paginate(10);

        // Step 4: Mark "now" as when the user last saw the alerts
        $request->session()->put('reports_last_seen', now());

        // Step 5: Return the view with the low-stock items
        return view('reports.index', compact('lowStockItems'));
    }

    public function flush(Request $request)
    {
        // Step 1: Update all items that are under low stock or reorder threshold
        // Reset reorder_level and low_stock_threshold to 0 (or another default value)
        Inventory::whereColumn('quantity', '<=', 'reorder_level')
            ->orWhereColumn('quantity', '<=', 'low_stock_threshold')
            ->update([
                'reorder_level' => 0, // Reset reorder level to 0
                'low_stock_threshold' => 0 // Reset low stock threshold to 0
            ]);

        // Step 2: Redirect to the reports index with a success message
        return redirect()->route('reports.index')
                        ->with('success', 'All low-stock and reorder alerts have been cleared.');
    }
}
