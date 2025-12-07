<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        // 1. Basic Totals
        $totalBookings = Booking::count();
        $totalCourts = Court::where('is_active', true)->count();
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_price');

        // 2. Booking Status Breakdown (For Graph)
        $bookingCounts = Booking::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all keys exist to prevent JS errors
        $bookingBreakdown = [
            'completed' => $bookingCounts['completed'] ?? 0,
            'cancelled' => $bookingCounts['cancelled'] ?? 0,
            'pending'   => $bookingCounts['pending'] ?? 0,
            'confirmed' => $bookingCounts['confirmed'] ?? 0,
        ];

        // 3. Court Status Breakdown (For Graph)
        // active = 1, inactive = 0
        $courtCounts = Court::selectRaw('is_active, count(*) as count')
            ->groupBy('is_active')
            ->pluck('count', 'is_active')
            ->toArray();

        $courtBreakdown = [
            'active'   => $courtCounts[1] ?? 0,
            'inactive' => $courtCounts[0] ?? 0,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_bookings' => $totalBookings,
                'total_courts' => $totalCourts,
                'total_revenue' => number_format(abs($totalRevenue), 2),
                'booking_graph' => $bookingBreakdown, // <--- New Data
                'court_graph' => $courtBreakdown      // <--- New Data
            ]
        ]);
    }
}