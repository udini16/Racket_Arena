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
        // 1. Base Query: Only 'confirmed' or 'completed' bookings count as revenue
        $query = Booking::whereIn('status', ['confirmed', 'completed']);

        // 2. Financial Aggregates
        // We use abs() to ensure positive numbers if historical data has negatives
        $lifetimeRevenue = abs($query->sum('total_price'));
        
        $currentMonthRevenue = abs((clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price'));

        $todayRevenue = abs((clone $query)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price'));

        // 3. Revenue By Court Breakdown
        // Group by court name to see which court earns the most
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

        // 4. Recent Transactions (Latest 10)
        $recentTransactions = Booking::with(['user', 'court'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'user' => $b->user ? $b->user->name : 'Guest',
                    'court' => $b->court ? $b->court->name : 'Unknown',
                    'date' => $b->created_at->format('Y-m-d H:i:s'), // Format for frontend
                    'amount' => number_format(abs($b->total_price), 2, '.', '')
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'lifetime' => number_format($lifetimeRevenue, 2, '.', ''),
                'this_month' => number_format($currentMonthRevenue, 2, '.', ''),
                'today' => number_format($todayRevenue, 2, '.', ''),
                'by_court' => $revenueByCourt,
                'recent' => $recentTransactions
            ]
        ]);
    }
}