<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\User;
use App\Models\Court;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        // 1. Total Bookings (All time)
        $totalBookings = Booking::count();

        // 2. Active Courts
        $totalCourts = Court::where('is_active', true)->count();

        // 3. Total Revenue
        // Only count confirmed or completed bookings
        // We use ABS() in case old data has negative values, though ideally data should be clean
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_bookings' => $totalBookings,
                'total_courts' => $totalCourts,
                'total_revenue' => number_format(abs($totalRevenue), 2) // Ensure positive and formatted
            ]
        ]);
    }
}
