<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminRevenueController extends Controller
{
    public function index()
    {
        $query = Booking::whereIn('status', ['confirmed', 'completed']);

        // 1. Financial Aggregates
        $lifetimeRevenue = abs($query->sum('total_price'));
        
        $currentMonthRevenue = abs((clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price'));

        $todayRevenue = abs((clone $query)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price'));

        // 2. Revenue By Court Breakdown
        $revenueByCourt = (clone $query)->with('court')->get()
            ->groupBy(function($booking) {
                return $booking->court ? $booking->court->name : 'Unknown/Deleted';
            })
            ->map(function($bookings, $courtName) {
                return [
                    'name' => $courtName,
                    'total' => abs($bookings->sum('total_price'))
                ];
            })
            ->sortByDesc('total')
            ->values();

        // 3. GRAPH DATA: Monthly Trend (Current Year)
        // We select the Month index (1-12) and the sum of prices
        $monthlyData = (clone $query)
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                // Key is month number, Value is total
                return [$item->month => abs($item->total)];
            });

        // Ensure we have data for all 12 months (fill 0 if empty)
        $graphDataset = [];
        for ($i = 1; $i <= 12; $i++) {
            $graphDataset[] = $monthlyData->get($i, 0);
        }

        // 4. Recent Transactions
        $recentTransactions = Booking::with(['user', 'court'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'user' => $b->user ? $b->user->name : 'Guest',
                    'date' => $b->created_at->format('Y-m-d H:i:s'),
                    'amount' => number_format(abs($b->total_price), 2, '.', '')
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'lifetime' => number_format($lifetimeRevenue, 2, '.', ''),
                'this_month' => number_format($currentMonthRevenue, 2, '.', ''),
                'today' => number_format($todayRevenue, 2, '.', ''),
                'graph_data' => $graphDataset, // <--- New Data for Chart
                'by_court' => $revenueByCourt,
                'recent' => $recentTransactions
            ]
        ]);
    }
}